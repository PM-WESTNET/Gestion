<?php
use app\modules\sale\models\Customer;
header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=customers_by_node.xls');
?>

<table>
<tr>
    <th>Cliente</th>
    <th>Codigo Cliente</th>
    <th>Fecha</th>
    <th>CBU</th>
    <th>Error</th>
    <th>Importe</th>
</tr>
<?php foreach ($dataProviderFailedPayments as $key => $value): ?>
   
    <tr>
        <td>
            <?php $customer = Customer::findOne(['code' => $value->customer_code]);
                echo $customer->fullName;?>
        </td>
        <td><?=$value->customer_code?></td>
        <td><?=$value->date?></td>
        <td><?=$value->cbu?></td>
        <td><?=$value->error?></td>
        <td><?=$value->amount?></td>
    </tr> 
<?php endforeach ?>

</table> 
