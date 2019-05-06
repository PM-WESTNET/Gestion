<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:46
 */

namespace app\modules\westnet\commands;

use app\modules\accounting\behaviors\AccountMovementBehavior;
use app\modules\afip\models\TaxesBook;
use app\modules\afip\models\TaxesBookItem;
use app\modules\checkout\models\Payment;
use app\modules\config\models\Config;
use app\modules\paycheck\models\Paycheck;
use app\modules\provider\models\Provider;
use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderPayment;
use app\modules\sale\components\CodeGenerator\CodeGeneratorFactory;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\modules\contract\components\ContractLowService;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\components\DebtorEvolutionService;
use app\modules\westnet\models\Node;
use Yii;
use yii\base\Event;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

class UtilsController extends Controller
{

    /**
     * Actualiza los codigos de pago con el codigo de pagofacil.
     * Solo lo hace para codigos de 14 digitos.
     */
    public function actionUpdatePaymentCode()
    {
        $customers = Customer::find()->where('length(payment_code) = 14')->all();

        $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');

        $i = 1;
        foreach ($customers as $customer) {
            if (!$generator->validate($customer->payment_code)) {
                $oldcode = $customer->payment_code;
                $company_code = $customer->company->code;

                $code = str_pad($company_code, 4, "0", STR_PAD_LEFT) . ($company_code == '9999' ? '' : '000') .
                    str_pad($customer->code, 5, "0", STR_PAD_LEFT);

                $customer->payment_code = $generator->generate($code);
                if ($oldcode != $customer->payment_code) {
                    $customer->save();
                }
                error_log($i . " - " . $customer->name . " De: " . $oldcode . " a " . $customer->payment_code);
                $i++;
            }
        }

    }

    /**
     * Verifica las ips que puede generar un nodo
     * @param $node_id
     */
    public function actionValidateIp($node_id)
    {
        $node = Node::findOne(['node_id' => $node_id]);
        for ($i = 0; $i < 250; $i++) {
            echo long2ip($node->getUsableIp()) . "\n";
        }
    }

    /**
     * Valida el codigo pasado con Pago Facil.
     * @param $code
     * @return string
     */
    public function actionValidatePagofacil($code)
    {
        $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
        echo "Code: " . $code . " => " . ($generator->validate($code) ? "ok" : "no") . "\n";
    }

    /**
     * Genera un codigo Pago facil con el customer_code pasado.
     * @param $customer_code
     */
    public function actionGeneratePagofacil($customer_code)
    {
        $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
        echo "Code: " . $customer_code . " => " . $generator->generate($customer_code) . "\n";
    }


    public function actionCreateTickets()
    {
        $contracts = [
            [24377, 13481, 43],
            [24378, 13482, 29],
            [24379, 13483, 29],
            [24380, 13484, 29],
        ];
        foreach ($contracts as $contract) {
            $ticket = new Ticket();
            $ticket->contract_id = $contract[0];
            $ticket->customer_id = $contract[1];
            $ticket->category_id = 31;
            $ticket->title = Yii::t('app', 'Instalation Ticket');
            $ticket->content = Yii::t('app', 'Instalation Ticket');
            $ticket->status_id = Config::getValue('ticket_new_status_id');
            $ticket->user_id = $contract[2];
            $ticket->setUsers([$contract[2]]);
            $ticket->save();
        }
    }

    public function actionCreateMovements($tipo = 0)
    {
        $amb = new AccountMovementBehavior();
        $amb->init();

        if ($tipo == 1) {
            $rs = Yii::$app->db->createCommand('SELECT * FROM to_delete')->queryAll();

            foreach ($rs as $key => $value) {

                $query = null;
                /** @var Query $query */
                $query = Yii::createObject(ActiveQuery::className(), [$value['class']]);

                switch ($value['class']) {
                    case 'app\\modules\\checkout\\models\\Payment':
                        $obj = $query->where(['payment_id' => $value['model_id']])->one();
                        break;
                    case 'app\\modules\\provider\\models\\ProviderBill':
                        $obj = $query->where(['provider_bill_id' => $value['model_id']])->one();
                        break;
                    case 'app\\modules\\provider\\models\\ProviderPayment':
                        $obj = $query->where(['provider_payment_id' => $value['model_id']])->one();
                        break;
                    case 'app\\modules\\sale\\models\\Bill':
                        $obj = $query->where(['bill_id' => $value['model_id']])->one();
                        break;
                }
                $obj->status = ($obj->status == 'cancelled' ? 'cancelled' : 'closed');
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
                echo 'model_id: ' . $value['model_id'] . "\n";
            }
        } else if ($tipo == 2) {
            $rs = Yii::$app->db->createCommand('SELECT * FROM to_delete_ecopago')->queryAll();

            foreach ($rs as $key => $value) {
                $obj = Payment::findOne(['payment_id' => $value['payment_id']]);

                $obj->status = 'closed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);

                $obj->status = 'cancelled';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }
        } else if ($tipo == 3) {
            $rs = Yii::$app->db->createCommand('SELECT * FROM to_delete_comp_prov')->queryAll();

            foreach ($rs as $key => $value) {
                echo $value['provider_bill_id'];
                $obj = ProviderBill::findOne(['provider_bill_id' => $value['provider_bill_id']]);

                $obj->status = 'closed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }
        } else if ($tipo == 4) {
            $ids = [33852, 33853, 33854, 33855, 33859, 33860, 33861, 33862, 33864, 33865, 33869, 33870, 33872, 33873, 33874, 33875, 33877, 33884, 33886, 33890, 33891, 33910, 33912, 33913, 33921, 33922, 33923, 33939, 33940, 33941, 33943, 33944, 33951, 33957, 33958, 33959, 33966, 33967, 33967, 33969, 33972, 34015, 34017, 34018, 34019, 34020, 34023, 34024, 34025, 34026, 34027, 34029, 34030, 34031, 34032, 34037, 34038, 34040, 34041, 34043, 34044, 34045, 34046, 34047, 34055, 34056, 34057, 34066, 34067, 34068, 34070, 34074, 34076, 34077, 34078, 34087, 34089, 34093, 34094, 34102, 34114, 34118, 34119, 34120, 34136, 34137, 34138, 34140, 34142, 34144, 34145, 34148, 34152, 34163, 34199, 34711, 33863, 33871, 34407, 34423, 34425, 34429, 34426, 34424, 34428, 34346, 34340, 34344, 34343, 34342, 34341, 34345, 33866, 34427, 33882, 34035, 33879, 33887, 34151, 34415,
                34147, 33876, 33971, 34356, 34033, 34148, 34300, 33883, 33961, 33962, 34149, 34141, 34150, 34273, 34274, 34354, 33963, 33964, 34034, 34035, 34271, 34139, 33881, 34417, 34303, 33956, 33952, 33953, 33905, 33901, 33902, 33903, 33986, 33999, 33981, 33979, 34132, 33904, 33975, 33984, 34092, 34314, 34315, 34357, 34370, 33878, 34005, 33888, 34128, 33997, 33889, 33978, 34003, 33990, 33893, 34127, 33989, 33898, 34008, 33980, 33868, 33895, 33976, 33994, 33892, 34009, 33900, 33894, 34014, 33988, 33907, 33991, 34192, 34134, 33991, 33908, 33987, 34353, 34204, 33960, 33885, 33945, 34085, 34079, 33946, 34408, 34002, 34113, 34112, 34028, 34390, 34039, 34106, 33998, 34007, 33909, 34135, 33985, 33864, 33937, 34141, 33974, 33968, 34042, 34107, 33294, 33911, 34098, 34146, 33857, 34439, 33856, 34416, 34036, 34251, 34106, 34349, 33919, 33914, 33918, 33915, 33917, 33916, 33920, 34137, 34212];

//            $ids = [33914, 33915, 33916, 33917, 33918, 33919, 33920, 34340, 34344, 34423, 34424, 34425, 34426, 34427, 34427, 34428, 34429, 33952, 33953, 34417, 34036, 33968, 33937, 33962, 34141, 34149, 34150, 34033, 33971, 34147, 33974, 34349, 34439];
//            $ids = [33852, 33853, 33854, 33855, 33859, 33860, 33861, 33862, 33864, 33865, 33869, 33870, 33872, 33873, 33874, 33875, 33877, 33884, 33886, 33890, 33891, 33910, 33912, 33913, 33921, 33922, 33923, 33939, 33940, 33941, 33943, 33944, 33951, 33957, 33958, 33959, 33966, 33967, 33967, 33969, 33972, 34015, 34017, 34018, 34019, 34020, 34023, 34024, 34025, 34026, 34027, 34029, 34030, 34031, 34032, 34037, 34038, 34040, 34041, 34043, 34044, 34045, 34046, 34047, 34055, 34056, 34057, 34066, 34067, 34068, 34070, 34074, 34076, 34077, 34078, 34087, 34089, 34093, 34094, 34102, 34114, 34118, 34119, 34120, 34136, 34137, 34138, 34140, 34142, 34144, 34145, 34148, 34152, 34163, 34199, 34711];
            foreach ($ids as $value) {
                $obj = ProviderBill::findOne(['provider_bill_id' => $value]);

                $obj->status = 'closed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }
        } else if ($tipo == 5) {
            $ids = [485133, 485134, 485135, 485136, 485137, 485138, 485139, 485140, 485141, 485142, 485143, 485144, 485145, 485146, 485147, 485148, 485149, 485150, 485151, 485152, 485153, 485154, 485155, 485156, 485157, 485158, 485159, 485160, 485161, 485162, 485163, 485164, 485165, 485166, 485167, 485168, 485169, 485170, 485171, 485172, 485173, 485174, 485175, 485176, 485177, 485178, 485179, 485180, 485181, 485182, 485183, 485184, 485185, 485186, 485187, 485188, 485189, 485190, 485191, 485192, 485193, 485194, 485195, 485196, 485197, 485198, 485199, 485200, 485201, 485202, 485203, 485204, 485205, 485206, 485207, 485208, 485209, 485210, 485211, 485212, 485213, 485214, 485215, 485216, 485217, 485218, 485219, 485220, 485221, 485222, 485223, 485224, 485225, 485226, 485227, 485228, 485229, 485230, 485231, 485232, 485233, 485234, 485235, 485236, 485237, 485238, 485239, 485240, 485241, 485242, 485243, 485244, 485245, 485246, 485247, 485248, 485249, 485250, 485251, 485252, 485253, 485254, 485255, 485256, 485257, 485258, 485259, 485260, 485261, 485262, 485263, 496864, 496899, 496944, 497088, 497090, 497091, 497094, 497373, 497427, 497453, 497460, 498186, 498187, 498263, 498264, 498349, 498362, 498380, 498381, 498382, 498383, 498384, 498385, 498386, 498387, 498388, 498389, 498390, 498391, 498392, 498393, 498394, 498395, 498396, 498397, 498398, 498399, 498400, 498401, 498402, 498403, 498404, 498405, 498406, 498407, 498408, 498409, 498410, 498411, 498412, 498413, 498414, 498415, 498416, 498417, 498418, 498419, 498420, 498421, 498422, 498423, 498424, 498425, 498426, 498427, 498428, 498429, 498430, 498431, 498432, 498433, 498434, 498435, 498436, 498437, 498438, 498439, 498440, 498441, 498442, 498443, 498444, 498445, 498446, 498447, 498448, 498449, 498450, 498451, 498452, 498453, 498454, 498455, 498456, 498457, 498458, 498459, 498460, 498461, 498462, 498463, 498464, 498465, 498466, 498467, 498468, 498469, 498470, 498471, 498472, 498473, 498474, 498475, 498476, 498477, 498478, 498479, 498480, 498481, 498482, 498483, 498484, 498485, 498486, 498487, 498488, 498489, 498490, 498491, 498492, 498493, 498494, 498495, 498496, 498497, 498498, 498499, 498500, 498501, 498502, 498503, 498504, 498505, 498506, 498507, 498508, 498509, 498510, 498511, 498512, 510500, 511253, 511652, 511653, 511714, 511715, 511716, 511717, 511718, 511719, 511720, 511721, 511722, 511723, 511724, 511725, 511726, 511727, 511728, 511729, 511730, 511731, 511732, 511733, 511734, 511735, 511736, 511737, 511738, 511739, 511740, 511741, 511742, 511743, 511744, 511745, 511746, 511747, 511748, 511749, 511750, 511751, 511752, 511753, 511754, 511755, 511756, 511757, 511758, 511759, 511760, 511761, 511762, 511763, 511764, 511765, 511766, 511767, 511768, 511769, 511770, 511771, 511772, 511773, 511774, 511775, 511776, 511777, 511778, 511779, 511780, 511781, 511782, 511783, 511784, 511785, 511786, 511787, 511788, 511789, 511790, 511791, 511792, 511793, 511794, 511795, 511796, 511797, 511798, 511799, 511800, 511801, 511802, 511803, 511805, 511806, 511807, 511808, 511809, 511810, 511811, 511812, 511813, 511814, 511815, 511816, 511817, 511818, 511819, 511820, 511821, 511822, 511823, 511824, 511825, 511826, 511827, 511828, 511829, 511830, 511831, 511832, 511833, 511834, 511835, 511836, 511837, 511838, 511839, 511840, 511841, 511842, 511843, 511844, 511845, 523686, 523687, 523688, 523689, 523690, 523691];
            //$ids = [];
            foreach ($ids as $value) {
                $obj = Bill::findOne(['bill_id' => $value]);

                $obj->status = 'completed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }
        } else if ($tipo == 6) {
            $ids = [46952, 46801, 47118, 47119, 46737, 47402, 47818, 46753, 47001, 47214, 46971, 46979, 46973, 46974, 46975, 46976, 46748, 46825, 47078, 47249, 47145, 47183, 47264, 47146, 46824, 47216, 47217, 46920, 46953, 46954, 46961, 46953, 46962, 46960, 46826, 47849, 47945, 47595];

            foreach ($ids as $value) {
                $obj = ProviderPayment::findOne(['provider_payment_id' => $value]);

                $obj->status = 'closed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }
        } else if ($tipo == 7) {
            $rs = Yii::$app->db->createCommand("select bill_id from bill b 
              left join account_movement_relation amr on b.bill_id = amr.model_id where b.date = '2016-11-01'
              and amr.account_movement_relation_id is null
            ")->queryAll();

            foreach ($rs as $key => $value) {
                $obj = Bill::findOne(['bill_id' => $value]);

                $obj->status = 'completed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }


        } else if ($tipo == 8) {
            $rs = Yii::$app->db->createCommand("select bill_id from bill b 
              left join account_movement_relation amr on b.bill_id = amr.model_id and amr.class = 'app\\modules\\sale\\models\\bills\\Bill'
              where b.status <> 'draft' and amr.account_movement_relation_id is null and b.partner_distribution_model_id is not null
            ")->queryAll();

            foreach ($rs as $key => $value) {
                error_log($value['bill_id']);
                $obj = Bill::findOne($value);

                $obj->status = 'completed';
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }


        }


        //
        /*
                $bills = Bill::find()
                    ->andWhere(['>=', 'date', '2016-07-18'])->all();

                foreach ($bills as $bill) {

                    $bill->status = "completed";
                    $bill->billType->invoice_class_id = null;
                    echo $bill->bill_id;
                    $event = new Event();
                    $event->sender = $bill;
                    $amb->afterUpdate($event);
                    exit();
                }*/

        /**
         * Listo las facturas de proveedores para poder generarle los movimientos
         *
         * $providerBills = ProviderBill::find()
         * ->where('provider_bill_id >= 34472')->all();
         *
         * foreach($providerBills as $bill) {
         * $bill->status = "closed";
         *
         * $event = new Event();
         * $event->sender = $bill;
         * $amb->afterUpdate($event);
         * exit();
         * }*/

    }

    public function actionUpdateProductToInvoice()
    {
        $ptis = (new Query())
            ->select(['contract_detail_id'])
            ->from('product_to_invoice')
            ->where('status = \'active\' AND period in( \'2016-10-01\') AND contract_detail_id IS NOT NULL AND funding_plan_id is not null')
            ->groupBy(['contract_detail_id'])
            ->having(['>', 'count(*)', 1])->all();


        foreach ($ptis as $pti) {
            $period = new \DateTime('2016-11-01');
            for ($i = 1; $i <= 6; $i++) {

                if (ProductToInvoice::find()
                        ->where(['contract_detail_id' => $pti['contract_detail_id']])
                        ->andWhere(new Expression('date_format(period, \'%Y-%m\') = \'' . $period->format('Y-m') . '\''))
                        ->andWhere(['status' => 'active'])
                        ->count() == 0
                ) {

                    $p = ProductToInvoice::find()
                        ->where(['contract_detail_id' => $pti['contract_detail_id']])
                        ->andWhere(new Expression('date_format(period, \'%Y-%m\') = \'2016-10\''))
                        ->andWhere(['status' => 'active'])
                        ->limit(1)
                        ->one();
                    echo ProductToInvoice::find()
                        ->where(['contract_detail_id' => $pti['contract_detail_id']])
                        ->andWhere(new Expression('date_format(period, \'%Y-%m\') = \'2016-10-01\''))
                        ->andWhere(['status' => 'active'])
                        ->limit(1)->createCommand()->getRawSql();
                    $p->period = $period->format('Y-m-d');
                    $p->update(false);
                    break;
                }
                $period->modify('first day of next month');
            }
        }
    }

    public function actionCreateDepositedPaycheckMovements()
    {
        $ptis = Paycheck::find()
            ->where(['status'=>'deposited'])
            ->andWhere(new Expression("paycheck_id not in ( select model_id from account_movement_relation where class = 'app\\modules\\paycheck\\models\\Paycheck')"))
            ->all();

        $amb = new AccountMovementBehavior();
        $amb->init();
        foreach ($ptis as $pti) {
            $event = new Event();
            $event->sender = $pti;
            $amb->afterUpdate($event);
        }
    }

    public function actionCreateMovement($id)
    {
        if ($id) {
            $data = [47217, 46920, 47849, 47945, 47595, 48278, 48517, 48534, 48538, 48542, 48543, 48544, 48545, 48546, 48548, 48551, 48552, 48553, 48557, 48567, 48568, 48569, 48580, 48587, 48588, 48590, 48593, 48595, 48597, 48600, 48662, 48664, 48676, 48678, 48679, 48681, 48685, 48703, 48716, 48726, 48737, 48738, 48740, 48742, 48743, 48754, 48764, 48782, 48787, 48788, 48792, 48796, 48795, 48826, 48849, 48850, 48853, 48855, 48856, 48857, 48860, 48862, 48865, 49735];
            $amb = new AccountMovementBehavior();
            $amb->init();

            foreach($data as $id) {
                $obj = ProviderPayment::findOne(['provider_payment_id' => $id]);
                //var_dump( $obj->getAmounts() );

                //$obj->status = 'completed';
                $obj->status = ($obj->status == 'cancelled' ? 'cancelled' : 'closed'); // ProviderBill
                $event = new Event();
                $event->sender = $obj;
                $amb->afterUpdate($event);
            }
        }
    }

    public function actionCreateEmptyConnections()
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $all = Yii::$app->db->createCommand('select cus.customer_id, cus.address_id, n.server_id, n.node_id as node_id from cus_nodo_csv csv
            left join customer cus on csv.customer = cus.code
            left join node n on csv.subnet = n.subnet')->query()->readAll();

            foreach ($all as $key=>$value) {
                $sqlContract = "INSERT INTO contract ( customer_id, date, to_date, from_date, status, address_id, description, vendor_id, external_id, tentative_node, print_ads, instalation_schedule) VALUES (%s, '%s', '%s', '%s', 'inactive', %s, '', 1, null, %s, 0, null);";
                $sqlContractDetail = "INSERT INTO contract_detail (contract_id, product_id, from_date, to_date, status, funding_plan_id, date, discount_id, count, vendor_id, applied) 
                            VALUES (%s, 7, '%s', '%s', 'low', null, '%s', null, 1, 1, 0);";

                $connection = "INSERT INTO connection (contract_id,node_id, server_id, ip4_1, ip4_2, ip4_public, status, due_date, company_id, payment_code, status_account, clean, old_server_id)
                          VALUES (%s, %s, 4, '0', '0', '0', 'disabled', null, 4, null, 'disabled', 0, null);";

                echo 'Customer: ' . $value['customer_id']  . ' - node_id: ' . $value['node_id'];
                if($value['node_id'] && $value['customer_id']) {
                    $date = '2017-01-01';
                    Yii::$app->db->createCommand( sprintf($sqlContract,
                        $value['customer_id'], $date, $date, $date, ($value['address_id'] ? $value['address_id']: 1 ), $value['node_id']
                    ) )->execute();

                    $contract_id = Yii::$app->db->createCommand('SELECT LAST_INSERT_ID() AS contract_id')->query()->read();
                    $contract_id = $contract_id['contract_id'];
                    var_dump($contract_id);
                    Yii::$app->db->createCommand( sprintf($sqlContractDetail,
                        $contract_id, $date, $date, $date
                    ) )->execute();

                    Yii::$app->db->createCommand( sprintf($connection,
                        $contract_id, $value['node_id'], $value['server_id']
                    ) )->execute();

                }
            }
            $trans->commit();
        } catch(\Exception $ex){
            $trans->rollBack();
        }
    }

    public function actionAfipBookFix()
    {
        $books = TaxesBook::find()->where(['type'=>'buy'])->all();

        /** @var TaxesBook $book */
        foreach($books as $book) {

            TaxesBookItem::updateAll(['page'=> 0], ['taxes_book_id'=>$book->taxes_book_id]);

            $tbis = TaxesBookItem::find()
                ->leftJoin('provider_bill pb', 'taxes_book_item.provider_bill_id = pb.provider_bill_id')
                ->where('taxes_book_id = ' . $book->taxes_book_id)
                ->orderBy(['pb.date'=>SORT_ASC])->all()
            ;
            $page = 1;
            $i = 0;
            foreach ($tbis as $tbi) {
                $tbi->page = $page;
                $tbi->update(false);

                $i++;
                if(($i%30) == 0) {
                    $page++;
                    echo  "Libro: ".$book->taxes_book_id." - Page: " . $page . " - i: " . $i ."\n";
                }
            }
        }
    }

    public function actionUpdatePayedBill()
    {
        echo ((new \DateTime('now'))->format('d/m/Y H:i:s'));
        // Traigo todos los clientes que tengan algo pagado.
        $sql = "SELECT coalesce(b.customer_id, p.customer_id) as customer_id, bills, payments FROM (
                  select
                    b.customer_id,
                    sum(b.total * bt.multiplier) as bills
                  from bill b
                    left join bill_type bt on b.bill_type_id = bt.bill_type_id
                  group by b.customer_id
                ) b left join (
                  select
                    p.customer_id,
                    sum(i.amount) as payments
                  from payment p
                    left join payment_item i on p.payment_id = i.payment_id
                  group by p.customer_id
                ) p on b.customer_id = p.customer_id
                where round(payments) > 0
                ;";

        // Borro todas las relaciones
        Yii::$app->db->createCommand('DELETE FROM bill_has_payment')->execute();
        Yii::$app->db->createCommand('UPDATE bill set payed = 0')->execute();

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        /*$result[] = [
            'customer_id' => 344
        ];*/
        // Recorro todos los clientes buscando
        foreach ($result as $customer) {
            echo $customer['customer_id']."\n";
            $sql = "SELECT p.payment_id, sum(i.amount) as saldo 
                        from payment p left join payment_item i on p.payment_id = i.payment_id 
                        WHERE p.customer_id = ".$customer['customer_id']." group by p.payment_id ORDER BY p.date;";
            $payments = Yii::$app->db->createCommand($sql)->queryAll();

            $sql = "select b.bill_id, round(b.total,2) as total, round(sum(bhp.amount),2) as pagado, round((b.total - sum(coalesce(bhp.amount,0))),2) as saldo
                                from bill b left join bill_has_payment bhp on b.bill_id = bhp.bill_id left join bill_type bt on b.bill_type_id = bt.bill_type_id
                                WHERE bt.multiplier >0 and b.payed = 0 and b.customer_id = ".$customer['customer_id']." GROUP BY b.bill_id, b.total ORDER BY b.date;";
            $bills = Yii::$app->db->createCommand($sql)->queryAll();

            foreach($payments as $payment) {
                $saldo = $payment['saldo'];

                $bills_passed = [];
                /** @var Bill $bill */
                foreach ($bills as $key=>$bill) {
                    if ($saldo > 0 && $bill['saldo'] > 0 && array_search($bill['bill_id'], $bills_passed)===false) {
                        $debt = $bill['saldo'];
                        $bhp = new BillHasPayment();
                        $bhp->bill_id = $bill['bill_id'];
                        $bhp->payment_id = $payment['payment_id'];

                        echo "saldo: " . $saldo . " - bills: ". $debt . " bill_id: ". $bill['bill_id'] . " - payment_id: ".$payment['payment_id'];
                        if ($saldo >= $debt) {
                            echo " - 0 - ";
                            $bhp->amount = $debt;
                            $saldo -= $debt;
                            $bills[$key]['saldo'] = 0;
                            Yii::$app->db->createCommand('UPDATE bill SET payed=1 where bill_id='.$bill['bill_id'])->execute();
                        } else if ($saldo < $debt) {
                            echo " - 1 - ";
                            $bhp->amount = $saldo;
                            $bills[$key]['saldo'] -= $saldo;
                            $saldo = 0;
                        }

                        echo " - saldo nuevo: ". $saldo;
                        if( $bills[$key]['saldo'] == 0) {
                            $bills_passed[] = $bill['bill_id'];
                        }
                        echo "\n";
                        $bhp->save();
                        if($saldo == 0) {
                            break;
                        }
                    }
                }
            }


        }
        echo ((new \DateTime('now'))->format('d/m/Y H:i:s'));
    }

    /**
     * Actualizo la evolucion de deuda con la fecha del hoy.
     *
     */
    public function actionUpdateDebtorEvolutionToday()
    {
        $service = new DebtorEvolutionService();
        $service->process(new \DateTime('now'));
    }

    /**
     * Actualizo la evolucion de deuda con la fecha enviada como parametro.
     * @param $date
     */
    public function actionUpdateDebtorEvolution($date)
    {
        $service = new DebtorEvolutionService();
        $service->process(new \DateTime($date));
    }

    /**
     * Actualizo la evolucion de deuda desde el 05-01-2016
     */
    public function actionUpdateDebtorEvolutionHistory()
    {
        $service = new DebtorEvolutionService();
        $inicio = new \DateTime('2016-01-05');
        $fin = new \DateTime('now');
        do {
            $fecha = $inicio;
            // Primer dia del mes
            echo "Procesando: " . $fecha->format('d/m/Y')."\n";
            $service->process($fecha);

            $fecha->add(new \DateInterval('P15D'));
            echo "Procesando: " . $fecha->format('d/m/Y')."\n";
            $service->process($fecha);

            $fecha->modify('last day of this month');
            echo "Procesando: " . $fecha->format('d/m/Y')."\n";
            $service->process($fecha);
            $fecha->modify('first day of next month');
            $fecha->add(new \DateInterval('P4D'));

            $inicio = $fecha;
        }while($inicio <= $fin);
    }

    /**
     * Paso los contratos con baja pendiente a baja.
     */
    public function actionLowContracts()
    {
        $service = new ContractLowService();
        $service->parseLowProcess();
    }

    public function actionCode()
    {
        try {
            $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
            $file = fopen('/tmp/clientes.csv', 'w');

            $sql = "select c.customer_id, c.code as codigo_cliente, co.code codigo_empresa,
       concat( c.name, ', ', c.lastname) as cliente,
       c.payment_code as codigo_de_pago
        from customer c left join company co on c.company_id = co.company_id
                left join contract con on c.customer_id = con.customer_id where c.status= 'enabled' and con.status = 'active'";
            fputcsv($file, [
                "customer_id",
                "codigo_cliente",
                "codigo_empresa",
                "cliente",
                "codigo_de_pago",
                "nuevo_codigo_pago"
            ]);

            $data = \Yii::$app->db->createCommand($sql)->query()->readAll();
            foreach($data as $row) {
                $code = str_pad($row['codigo_empresa'], 4, "0", STR_PAD_LEFT) . ($row['codigo_empresa'] == '9999' ? '' : '000' ) .
                    str_pad($row['codigo_cliente'], 5, "0", STR_PAD_LEFT) ;
                fputcsv($file, [
                    $row["customer_id"],
                    $row["codigo_cliente"],
                    $row["codigo_empresa"],
                    $row["cliente"],
                    $row["codigo_de_pago"],
                    $generator->generate($code)
                ]);
            }
            fclose($file);
        } catch (\Exception $ex) {
            echo $ex->getMessage()."\n";
        }

    }
}
