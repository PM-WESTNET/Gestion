<?php
use app\modules\config\models\Config;
?>

<tfoot style="background-color: #ff570c; color:white;">
    <tr>
      <td>
        <h6 style="margin-bottom: 5px; line-height: 1.5em; text-align: center; text-transform: uppercase;">Visitanos en</h6>
        <p style="text-align: center;">
          <a href="#" style="text-align: center; text-decoration: none; margin-top: 0px; margin-bottom: 5px; font-weight: 500; color: white; letter-spacing: 2px;">
            <?= $company ? $company->web : '' ?>
          </a>                
        </p>

        <hr style="width: 80%; margin: 0 auto;">

        <h5 style="margin: 10px; line-height: 1.5em; text-align: center; text-transform: uppercase;">Comunicate con Nosotros</h5>

        <p style="text-align: center;">
          <strong>Servicio Técnico: </strong> <?= Config::getValue('phone-st') ?>
        </p>

        <p style="text-align: center;">
          <strong>Administración: </strong> <?= Config::getValue('phone-admin') ?>
        </p>

        <p style="text-align: center;">
          <strong>Ventas: </strong>  <?= Config::getValue('phone-sellers') ?>
        </p>

      </td>
    </tr>
</tfoot>
