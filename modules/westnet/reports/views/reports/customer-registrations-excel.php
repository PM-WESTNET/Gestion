<?php
header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=customer_registrations.xls');
?>

<table>
<tr>
    <th>ID</th>
    <th>Codigo</th>
    <th>Nombre Completo</th>
    <th>Tecnologia</th>
    <th>Velocidad</th>
    <th>Nodo</th>
    <th>Fecha</th>
</tr>
<?php $aux = 1;?>
<?php foreach ($list_customers->allModels as $key => $value): ?>
    <?php 
        $tecnology = ''; 
        if(strpos(strtolower($value['name_product']),'ftth')){
            $tecnology = "FIBRA";
        }else if(strpos(strtolower($value['name_product']),'wifi')){
            $tecnology = "WIRELESS";
        }else{
            $tecnology = "Sin Identificar";
        }

        $speed = preg_match('/[0-9]/', $value['name_product'], $matches, PREG_OFFSET_CAPTURE);
        $speed = substr($value['name_product'],$matches[0][1]);
    ?>
    <tr>
    <td><?=$aux?></td>
    <td><?=$value['code']?></td>
    <td><?=$value['fullname']?></td>
    <td><?=$tecnology?></td>
    <td><?=$speed?></td>
    <td><?=$value['node']?></td>
    <td><?=$value['date']?></td>
    </tr>
    <?php $aux++;?>   
<?php endforeach ?>

</table>