<?php
header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=customers_by_node.xls');
?>

<table>
<tr>
    <th>Nodo</th>
    <th>Total</th>
</tr>
<?php $aux = 1;?>
<?php foreach ($dataProvider->allModels as $key => $value): ?>
    <tr>
    <td><?=$value['node']?></td>
    <td><?=$value['total']?></td>
    </tr> 
<?php endforeach ?>

</table>