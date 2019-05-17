<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\editable\Editable;
use app\modules\westnet\models\AdsPercentagePerCompany;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\AdsPercentagePerCompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ads Percentage Per Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ads-percentage-per-company-index">
    <div class="title">
        <h1> <?= Html::encode($this->title) ?> </h1> <br>
    </div>

    <div>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th><?php echo Yii::t('app', 'Companies') ?></th>
                <?php foreach ($parent_companies as $company) { ?>
                    <th><?php echo $company->name ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($companies as $company) { ?>
                <tr>
                    <td>
                        <?= $company->name ?>
                    </td>
                   <?php foreach ($parent_companies as $parent) {
                       echo '<td>';
                       if($parent->company_id == $company->parent_id) {
                           echo Editable::widget([
                               'name'=> "percentage",
                               'asPopover' => false,
                               'value' => AdsPercentagePerCompany::getCompanyPercentage($company->company_id),
                               'header' => 'Name',
                               'options' => ['class' => 'form-control status-class', 'id' => "$parent->company_id[$company->company_id]"],
                               'formOptions' => ['action' => Url::toRoute(['update-company-percentage', 'company_id' => $company->company_id])],
                               'size'=>'md',
                               'options' => ['class'=>'form-control', 'placeholder'=>'Enter person name...']
                           ]);
                           echo '%';
                       }
                        echo '</td>';
                    }?>
                </tr>

            <?php } ?>
            </tbody>
        </table>

        <h5 class="pull-right"> <?= Yii::t('app', 'The parent company percentage sum have to be 100%')?> </h5>

    </div>
</div>