<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\modules\sale\models\Company;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AdsPercentagePerCompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ads-percentage-per-company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_company_id')->widget(Select2::class, [
        'data' => ArrayHelper::map(Company::find()->where(['parent_id' => null])->all(), 'company_id', 'name'),
    ]) ?>

    <?= $form->field($model, 'company_id')->textInput() ?>

    <?= $form->field($model, 'percentage')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

    <script>
        var EmptyAds = new function () {
            this.init = function () {

                $('#adspercentagepercompany-parent_company_id').on('change', function () {
                    if ($('#adspercentagepercompany-parent_company_id').val() !== '') {
                        // EmptyAds.companyUsePaymentCard($('#company_id').val());
                    }
                });

            }

            this.companyUsePaymentCard = function (company_id) {
                $.ajax({
                    method: 'GET',
                    url: '<?= Url::to(['/sale/company/company-use-payment-card'])?>',
                    data: {'company_id': company_id},
                    dataType: 'json',
                }).done(function (json) {
                    if (json.status == 'success' && json.use_payment_card == true) {
                        EmptyAds.getUnusedPaymentCardsQty();
                    }
                });
            }
        }
    </script>

<?php $this->registerJS('EmptyAds.init();'); ?>