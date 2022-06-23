<?php

namespace app\controllers;

use app\modules\checkout\models\Payment;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\NotifyPayment;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\modules\sale\models\search\CustomerSearch;
use IPv4\SubnetCalculator;
use app\modules\westnet\notifications\models\PaymentIntentionAccountability;
use app\modules\sale\models\Bill;

class SiteController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $render_variables = array();
        //Si posee el rol, el  index debe ser la vista de agenda
        if(!Yii::$app->user->isGuest){
            if (Yii::$app->user->identity->hasRole('home_is_agenda', false)) {
                return $this->redirect(['/agenda/default/index']);
            }


            if(Yii::$app->user->identity->hasRole('User-alert-new-no-verified-tranferences', false) && NotifyPayment::transferenceNotifyPaymentsNotVerifiedExists()) {
                Yii::$app->session->addFlash('info', Yii::t('app', 'Theres one or more notify payments by transference not verified'));
            }

            // SIRO PAYMENTS ALERT
            if(Yii::$app->user->identity->hasRole('user-alert-non-verified-siro-payments', false))
            {
                $payment_intention_accountability = PaymentIntentionAccountability::find()->where(['status' => 'draft'])->all();
                if(count($payment_intention_accountability) > 0){
                    // push the amount of payment intentions in draft still
                    $render_variables['payment_intention_accountability'] = $payment_intention_accountability;
                }
            }

            // FACTURACION
            if(Yii::$app->user->identity->hasRole('batch-invoice-rol', false))
            {
                $bill_errors_count = (new Bill())->getErrorAndUnclosedBillsQuery()->count();
                if($bill_errors_count > 0){
                    // push the amount of payment intentions in draft still
                    $render_variables['bill_errors_count'] = $bill_errors_count;
                }
            }
            
            // CRONS

            // DD/DA firstdata.service

            //...
        }
        return $this->render('index', $render_variables);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        $calc = new SubnetCalculator('172.1.0.0', 16);

        echo print_r($calc->getIPAddressRange(), 1);
        die();
        return $this->render('about');
    }

    public function actionDebugCustomer($customer_id)
    {
        $debug = false;
        $estados = [];
        $estadosAnteriores = [];

        $estadosAnteriores[Connection::STATUS_ACCOUNT_ENABLED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_DISABLED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_FORCED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_DEFAULTER] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_CLIPPED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_LOW] = 0;

        $estados[Connection::STATUS_ACCOUNT_ENABLED] = 0;
        $estados[Connection::STATUS_ACCOUNT_DISABLED] = 0;
        $estados[Connection::STATUS_ACCOUNT_FORCED] = 0;
        $estados[Connection::STATUS_ACCOUNT_DEFAULTER] = 0;
        $estados[Connection::STATUS_ACCOUNT_CLIPPED] = 0;
        $estados[Connection::STATUS_ACCOUNT_LOW] = 0;

        $due_day = Config::getValue('bill_due_day');
        if (!$due_day) {
            $due_day = 15;
        }
        $newContractsDays = Config::getValue('new_contracts_days');
        if (!$newContractsDays) {
            $newContractsDays = 0;
        }

        $invoice_next_month = Config::getValue('contract_days_for_invoice_next_month');

        $due_date = new \DateTime(date('Y-m-') . $due_day);
        $due_forced = null;
        $date = new \DateTime('now');
        $newContracts = new \DateTime('now +' . $newContractsDays . " days");
        $last_bill_date = new \DateTime((new \DateTime('now - 1 month'))->format('Y-m-' . $invoice_next_month));
        $bill_date = new \DateTime('last day of this month');
        $first_date_of_this_month = new \DateTime( 'first day of this month');

        $subprice = (new Query())
            ->select(['product_id', new Expression('max(date) maxdate') ])
            ->from('product_price')
            ->groupBy(['product_id']);


        $query_plan = (new Query())
            ->select(['(net_price + taxes) as price'])
            ->from(['contract c'])
            ->leftJoin('contract_detail cd', 'c.contract_id = cd.contract_id')
            ->leftJoin('product p', 'cd.product_id = p.product_id')
            ->leftJoin('product_price pp', 'p.product_id = pp.product_id')
            ->innerJoin(['ppppim'=> $subprice], 'ppppim.product_id = pp.product_id and ppppim.maxdate = pp.date')
            ->where("c.status ='active' and p.type = 'plan' and c.customer_id = :customer_id and c.contract_id = :contract_id")
            ->orderBy(['pp.date'=>SORT_DESC])
        ;

        $customer = Customer::findOne($customer_id);

        $customerClass = $customer->getCustomerClass()->one();

        $aviso_date = clone $due_date;
        $cortado_date = clone $due_date;
        $aviso_date->modify('+' . $customerClass->tolerance_days . ' day');
        $cortado_date->modify('+' . $customerClass->days_duration . ' day');

        $contracts = [];

        // Si no tiene deuda o la deuda es menor a la tolerancia, habilito.
        $payment = new Payment();
        $payment->customer_id = $customer->customer_id;
        $amount = round($payment->accountTotal());
        $contracts = Contract::findAll(['customer_id' => $customer->customer_id]);

        //TODO Si el contrato tiene fecha de hoy y tiene deuda se corta
        foreach ($contracts as $contract) {
            $connection = Connection::findOne(['contract_id' => $contract->contract_id]);

            $precio_plan = clone $query_plan;

            $precioPlan = $query_plan->addParams([
                ':customer_id' => $contract->customer_id,
                ':contract_id' => $contract->contract_id
            ])->scalar();

            try {
                $from_date = new \DateTime(($contract->from_date ? $contract->from_date : $contract->date));
            } catch (\Exception $ex) {
                continue;
            }

            if ($connection) {
                $status_old = $connection->status_account;
                if (is_null($status_old)) {
                    $status_old = $connection->status_account = Connection::STATUS_ACCOUNT_DISABLED;
                }
                $estadosAnteriores[$connection->status_account]++;

                // Si la conexion esta forzada,
                // En el caso de que la fecha de forzado sea mayor a hoy, proceso normalmente, buscando deuda
                // y demas.
                if ($connection->status_account == Connection::STATUS_ACCOUNT_FORCED) {
                    $due_forced = $date;
                    try {
                        $due_forced = new \DateTime($connection->due_date);
                    } catch (\Exception $ex) {
                        $connection->due_date = null;
                    }
                    // Si la fecha de forzado es mayor a hoy, es porque todavia no se cumple y lo tengo que omitir
                    if ($due_forced >= $date) {
                        $estados[$connection->status_account]++;
                        continue;
                    }
                }

                $debtLastBill = 0;
                $cs = new CustomerSearch();
                $rs = $cs->searchDebtBills($customer->customer_id);
                if (!$rs) {
                    $debtLastBill = 0;
                } else {
                    $debtLastBill = $rs['debt_bills'];
                }

                $newContractsFromDate = clone $from_date;
                $newContractsFromDate->modify('+' . $newContractsDays . " days");

                /*if ($debug) {
                    var_dump(": " . $contract->customer_id . " " .
                        " - from: " . $from_date->format('Y-m-d') . " - newContracts: " . $newContracts->format('Y-m-d') .
                        " - newContractsFromDate: " . $newContractsFromDate->format('Y-m-d') .
                        " - aviso_date: " . $aviso_date->format('Y-m-d') .
                        " - cortado_date: " . $cortado_date->format('Y-m-d') .
                        " - due_date: " . $due_date->format('Y-m-d') .
                        " - due_forced: " . ($due_forced ? $due_forced->format('Y-m-d') : '') .
                        " - amount: " . $amount . " - tolerancia: " . $customerClass->percentage_tolerance_debt .
                        " - debtLastBill: " . $debtLastBill .
                        " - days: " . $date->diff($from_date)->days . " - newContractsDays: " . $newContractsDays
                    );
                }*/

                // Si no esta en proceso de baja
                if ($contract->status != Contract::STATUS_LOW_PROCESS &&
                    $contract->status != Contract::STATUS_LOW &&
                    $connection->status_account != Connection::STATUS_ACCOUNT_LOW
                ) {
                    /** Habilito
                     *  - es free o
                     *  - No tiene deuda o
                     *  - Tiene deuda menor al porcentaje de tolerancia y hoy es menor a la fecha de corte y menor a la fecha de corte por nuevo y debe una o menos facturas
                     *
                     */
                    $tiene_deuda = ($amount <= 0);
                    $tiene_deuda_sobre_tolerante = (round(abs($amount)) >= $customerClass->percentage_tolerance_debt);
                    $es_nueva_instalacion = ($date->diff($from_date)->days <= $newContractsDays);
                    $avisa = ($date >= $aviso_date && $date < $cortado_date);
                    $corta = ($date >= $cortado_date);
                    $es_nuevo = ($from_date >= $last_bill_date && $from_date <= $bill_date);
                    //Verificamos que la ultima factura cerrada sea del mes corriente.
                    $last_closed_bill = $customer->getLastClosedBill();
                    $last_closed_bill_date = $last_closed_bill ? (new \DateTime($last_closed_bill->date)) : false;
                    $lastBillItsFromActualMonth = $last_closed_bill_date ? ($date->format('Y-m') == $last_closed_bill_date->format('Y-m')) : true;

                   /* var_dump('$debtLastBill: '.$debtLastBill."<br>");
                    var_dump('$tiene_deuda: '.$tiene_deuda."<br>");
                    var_dump('$tiene_deuda_sobre_tolerante: '.$tiene_deuda_sobre_tolerante."<br>");
                    var_dump('$es_nueva_instalacion: '.$es_nueva_instalacion."<br>");
                    var_dump('$avisa: '.$avisa."<br>");
                    var_dump('$corta: '.$corta."<br>");
                    var_dump('$es_nuevo: '.$es_nuevo."<br>");
                    var_dump('$lastBillItsFromActualMonth: '.$lastBillItsFromActualMonth."<br>");
                    var_dump('Estado de la cuenta '.$connection->status_account."<br>");
                    var_dump('-----($lastBillItsFromActualMonth): '.($lastBillItsFromActualMonth )."<br>");
                    var_dump('-------($last_closed_bill_date): '.($last_closed_bill_date ? $last_closed_bill_date->format('Y-m-d') : $last_closed_bill_date)."<br>");


                    var_dump("-----------<br>");*/


                    if ($debug) {
                        error_log('tiene_deuda_sobre_tolerante: ' . ($tiene_deuda_sobre_tolerante ? 's' : 'n') . " - " . ' tiene_deuda: ' . ($tiene_deuda ? 's' : 'n')
                            . ' - es_nueva_instalacion: ' . ($es_nueva_instalacion ? 's' : 'n')
                            . ' - avisa: ' . ($avisa ? 's' : 'n')
                            . ' - corta: ' . ($corta ? 's' : 'n')
                            . ' - es_nuevo: ' . ($es_nuevo ? 's' : 'n')
                            . ' - last_bill_date: ' . $last_bill_date->format('Y-m-d'));

                    }


                    if (strtolower($customerClass->name) == 'free') {
                        //var_dump("IF FREE<br>");
                        $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                    } else if ($es_nueva_instalacion) {
                        //var_dump("IF $es_nueva_instalacion<br>");
                        $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                    } else if ($es_nuevo && $tiene_deuda && $tiene_deuda_sobre_tolerante) {
                        //var_dump('IF $es_nuevo && $tiene_deuda && $tiene_deuda_sobre_tolerante'."<br>");
                        $connection->status_account = Connection::STATUS_ACCOUNT_CLIPPED;
                        //error_log( $contract->customer_id . "\t" . $bills ."\t". $debtLastBill['debt_bills'] . "\t" .$debtLastBill2['payed_bills'] . "\t" .$debtLastBill . "\t" . $amount . "\t" . ceil($precioPlan) );
                        //error_log( $contract->customer_id . "\t" . $bills ."\t". $debtLastBill  . "\t" .$debtLastBill  . "\t" .$debtLastBill . "\t" . $amount . "\t" . ceil($precioPlan) );

                    } else if ($connection->status_account == Connection::STATUS_ACCOUNT_CLIPPED) {
                        //var_dump('IF Connection::STATUS_ACCOUNT_CLIPPED'."<br>");
                        //  $dateLastBill = new \DateTime($this->getLastBill($customer->customer_id));
                        /**
                         * Habilito si:
                         *  - solo debe la factura del mes actual y la fecha es menor a la de corte
                         */
                        if (!$tiene_deuda || ($tiene_deuda && !$tiene_deuda_sobre_tolerante) || ($lastBillItsFromActualMonth && $date < $cortado_date)) {
                           // var_dump("Entra a if 1\n");
                            $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                        }
                    } else if (
                    (!$tiene_deuda ||
                        ($tiene_deuda && !$tiene_deuda_sobre_tolerante) ||
                        ($tiene_deuda && $tiene_deuda_sobre_tolerante && $debtLastBill <= 1 && $lastBillItsFromActualMonth && !$corta && !$avisa)
                    )
                    ) {
                         /*var_dump('IF  (!$tiene_deuda ||
                        ($tiene_deuda && !$tiene_deuda_sobre_tolerante) ||
                        ($tiene_deuda && $tiene_deuda_sobre_tolerante && $debtLastBill <= 1 && $lastBillItsFromActualMonth && !$corta && !$avisa)'."<br>");
                        var_dump('---!$tiene_deuda: '.(!$tiene_deuda)."<br>");
                        var_dump('---($tiene_deuda && !$tiene_deuda_sobre_tolerante ):'.($tiene_deuda && !$tiene_deuda_sobre_tolerante )."<br>");
                        var_dump('---($tiene_deuda && $tiene_deuda_sobre_tolerante && $debtLastBill <= 1 && $lastBillItsFromActualMonth && !$corta && !$avisa ): '.($tiene_deuda && $tiene_deuda_sobre_tolerante && $debtLastBill <= 1 && $lastBillItsFromActualMonth && !$corta && !$avisa )."<br>");
                        var_dump('-----($debtLastBill <= 1): '.($debtLastBill <= 1)."<br>");
                        var_dump('-----($lastBillItsFromActualMonth): '.($lastBillItsFromActualMonth )."<br>");
                        var_dump('-------($last_closed_bill_date): '.($last_closed_bill_date ? $last_closed_bill_date->format('Y-m-d') : $last_closed_bill_date)."<br>");
			*/

                        $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;

                    } else if (($tiene_deuda && $tiene_deuda_sobre_tolerante) && $avisa && $debtLastBill <= 1) {
                        /**
                         * Deudor:
                         *  -  No esta en proceso de baja
                         *  -  Tiene deuda mayor a la tolerancia.
                         *  -  Hoy es mayor a la fecha de aviso y menor a la de corte o
                         *      - hoy es menor a la de aviso y menor a la de corte y debe mas de una factura
                         */
                        $connection->status_account = Connection::STATUS_ACCOUNT_DEFAULTER;
                    } else if (
                        (($tiene_deuda && $tiene_deuda_sobre_tolerante) &&
                            ($corta && $debtLastBill >= 1) ||
                            ($debtLastBill >= 1 && !$es_nueva_instalacion)) &&
                        (($connection->status_account == Connection::STATUS_ACCOUNT_FORCED && ($due_date && $due_forced ? $date > $due_forced : false)) || $connection->status_account != Connection::STATUS_ACCOUNT_FORCED) ||
                        $connection->status_account == Connection::STATUS_ACCOUNT_CLIPPED
                    ) {
                        /**
                         * Cortado
                         *  -  Si tiene deuda mayor a la tolerancia y
                         *      -  debe una o mas facturas y
                         *      -  hoy es mayor a la fecha de aviso y menor a la fecha de corte
                         *  -  Esta forzado y hoy es mayor a la fecha de forzado
                         *  -  No esta en proceso de baja
                         */
                        $connection->status_account = Connection::STATUS_ACCOUNT_CLIPPED;
                    }
                }

                //var_dump($connection->status_account);

            }
        }
    }
}
