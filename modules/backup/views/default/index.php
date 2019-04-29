<?php
$this->title = Yii::t('app', 'Backup');
?>
<div class="backup-default-index">

    <?php
    $this->params ['breadcrumbs'] [] = [
        'label' => Yii::t('backup','Manage'),
        'url' => array(
            'index'
        )
    ];
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php
            echo $this->render('_list', array(
                'dataProvider' => $dataProvider
            ));
            ?>
        </div>
    </div>

</div>