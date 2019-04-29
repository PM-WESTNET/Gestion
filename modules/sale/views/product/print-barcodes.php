<?php for($j = 0; $j < 9; $j++): ?>
    <div class="row">
        <?php for ($index = 0; $index < 4; $index++): ?>
        <div class="barcode">
            <img src="<?= \yii\helpers\Url::toRoute(['product/barcode','id'=>$model->product_id]) ?>" />
        </div>
        <?php endfor; ?>
    </div>
<?php endfor; ?>
