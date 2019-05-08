<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 08/05/19
 * Time: 12:32
 */
$this->title = Yii::t('app','Verify Emails');
?>


<div class="verify-emails">

    <h1 class="title"><?php echo $this->title ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t('app','Import files')?></h3>
        </div>
        <div class="panel-body">
            <form action="<?php echo \yii\helpers\Url::to(['/sale/customer/verify-emails'])?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfToken?>">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <label for="field"><?php echo Yii::t('app','Field to Verify')?></label>
                        <?php echo \yii\helpers\Html::radioList('field', 'email', [
                            'email' => Yii::t('app','Email'),
                            'email2' => Yii::t('app','Secondary Email')
                        ])?>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <label for="files"><?php echo Yii::t('app','Files')?></label>
                        <?php echo \yii\helpers\Html::fileInput('files', null, ['multiple' => true])?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php echo \yii\helpers\Html::submitButton(Yii::t('app','Verify'), ['class' => 'btn btn-success pull-right'])?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h3><?php echo Yii::t('app','Results')?></h3>
    <?php if (!empty($results)):?>
        <div class="alert alert-info">
            <h4><?php echo Yii::t('app','Total Emails').': '. $results['total']?></h4>
            <h4><?php echo Yii::t('app','Total Active').': '. $results['active']?></h4>
            <h4><?php echo Yii::t('app','Total Inactive').': '. $results['inactive']?></h4>
            <h4><?php echo Yii::t('app','Total Bounced').': '. $results['bounced']?></h4>
        </div>
    <?php endif;?>

</div>


