<?php

use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * 
 */
?>
<div style="margin: 0.5cm; ">
    <table style="border: #000 dashed 1px; width: 100%; padding: 0px; margin: 0px; max-width: 18cm; text-align: center;">
        <tr style=" width: 100%; height: 200px">
            <td style=" width: 50%; text-align: center;">
                <p>
                    <img  width="130" src="<?=Url::base(true) . "/" . $company->getLogoWebPath()?>">
                </p>
                <p>
                    <strong><?= $name ?></strong>
                </p>
            </td>
            <td style=" width: 50%; border-left: #000 dashed 1px;  ">
                <table style="width:100%;">
                    <tr>
                        <td>
                            <p>
                                <strong><?= $name ?></strong><br/>
                                <strong><?= $code ?></strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img width="50%" height="50%" src="<?= Url::to(['/sale/customer/barcode', 'code' => $code], true) ?>" />
                        </td>
                    </tr>
                </table>   
            </td>    
        </tr>
    </table>
</div>