<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Billing Config');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-has-billing-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <form id="chb" action="<?php echo \yii\helpers\Url::to(['company-has-billing/save']) ?>" method="POST">
        <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->csrfToken?>"/>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo Yii::t('app', 'Bill Type') ?></th>
                    <?php foreach ($parent_companies as $company) { ?>
                        <th><?php echo $company->name ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($billTypes as $billType) { ?>
                    <tr>
                        <td>
                            <?php echo $billType->name ?>
                        </td>
                        <?php
                            foreach ($parent_companies as $company) {
                                $company_id = 0;
                                if(array_key_exists($company->company_id, $types )!==false) {
                                    if(array_key_exists($billType->bill_type_id, $types[$company->company_id] )!==false) {
                                        $company_id = $types[$company->company_id][$billType->bill_type_id];
                                    }
                                }

                                echo '<td>'. Html::dropDownList('chb['.$company->company_id.']['.$billType->bill_type_id.']', $company_id, \yii\helpers\ArrayHelper::map($companies, 'company_id', 'name'), [
                                    'class' => 'form-control',
                                    'separator' => '<br>'
                                ]). '</td>';
                            }
                        ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-12 text-right">
                <button class="btn btn-primary" id="save-chb" type="submit"><?php echo Yii::t('app', 'Save') ?></button>
            </div>
        </div>
    </form>

</div>