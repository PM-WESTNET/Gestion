<?php

use yii\helpers\Html;
use yii\helpers\Url;

$company_name = strtolower(Yii::$app->params['gestion_owner_company']);
$company_images = Yii::$app->params['company_images'];

// // image_folder path from webroot 
// var_dump(Yii::getAlias('@webroot'));
// $image_folder = Yii::getAlias('@webroot').'/images/';
// $image_folder = '/images/';
// var_dump($image_folder);
// // $image_folder = '';
// var_dump(Url::base());
// // var_dump(Url::base(''));
// var_dump(__FILE__);
// var_dump(Yii::getAlias('@webroot/images/'));
// var_dump(Yii::getAlias('@web'));
$image_folder = '/images/';
?>
<div class="title">
    <h1>
        All images of company (owner)
    </h1>
</div>
<div class="col-md-12 row ">

    <!-- for each image defined on app params.php file -->
    <?php foreach ($company_images as $image_key_name => $image_obj) : ?>
        <?php $path = $image_folder.$image_obj ?>
        <div>
            <p> <?= Yii::t('app', 'Name') . ": " . '"' . $image_key_name . '"' ?> </p>
                <?= Html::img($path, ['alt' => $company_name, 'style' => 'margin: 0 auto; width: 140px;']) ?>
            <p> 
                <?php 
                    echo Yii::t('app', 'File path') . ": " . '"' . $path . '"' . '</br>' ;
                    if(!file_exists(Yii::getAlias('@webroot'.$path))) echo "<span class='label label-danger'>file not found</span></br>";
                ?>
            </p>
        </div>
        <hr>

    <?php endforeach; ?>
</div>