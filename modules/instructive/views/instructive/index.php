<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\instructive\models\InstructiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Instructives');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructive-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (\webvimark\modules\UserManagement\models\User::canRoute('/instructive/instructive/create')):?>
    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app','Create Instructive'),
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    <?php endif;?>

    <hr>

    <?php if (count($categories) > 0):?>
        <?php foreach ($categories as $category):?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"
                        <a role="button" data-toggle="collapse" href="#collapse<?php echo $category->instructive_category_id?>" aria-expanded="false" aria-controls="collapse<?php echo $category->instructive_category_id?>">
                            <?php echo $category->name?>
                        </a>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="collapse<?php echo $category->instructive_category_id?>">
                    <div class="list-group">
                        <?php
                        echo \yii\widgets\ListView::widget([
                            'dataProvider' => (new \yii\data\ActiveDataProvider(['query' => $category->getInstructives(), 'pagination' => false])),
                            'itemView' => '_instructive',
                            'summary' => ''

                        ])

                        ?>
                    </div>
                </div>
            </div>


        <?php endforeach;?>
    <?php else:?>
        <h4><?php echo Yii::t('app','There are no instructions available') ?></h4>
    <?php endif;?>


</div>
