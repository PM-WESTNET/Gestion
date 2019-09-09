# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]

## [2019.09.09.1] - 09-09-2019
## Agregado
 - Se agrega comando de consola para corregir comprobantes a proveedor con neto en impuestos mal cargados.
  
## [2019.09.09.0] - 09-09-2019
## Agregado
 - Panel de gestiones de cada cliente, con vínculo a ticket.
### Modificado
 - Se modifica consulta de tickets, ya que salteaba registros el paginador de yii

## [2019.09.06.0] - 06-09-2019
### Agregado
 - Al activar un contrato, se genera un ticket de la categoría de Gestion de Ads.
### Modificado
 - En panel de gestión de instalaciones, se muestran tambien los tickets de la categoría de gestion de ADS.
 
## [2019.09.05.0] - 05-09-2019
### Agregado
 - Se agrega gestion de ticket desde modal de creacion de observacion.
 - Se agregan tipos de gestion de tickets. Campos nuevos en tabla ticket_management.
 
## [2019.09.04.1] - 04-09-2019
### Modificado
 - Consulta de clientes activos en mobile app.
 
## [2019.09.04.0] - 04-09-2019
### Agregado
 - Se agrega a reporte de APP cantidad de extensiones de pago por periodo.
### Modificado
 - Se modifica reporte de APP. Cantidad de informes de pago agrupados por periodo y medio de pago.
 
## [2019.09.03.0] - 03-09-2019
### Agregado
 - Se agrega en reporte de aplicación, cantidad de informes de pago realizados, por medio de pago.
### Modificado
 - Se cambia reporte de APP para que en "Clientes Activos" solo muestre aquellos que poseen una conexion activa.
 
## [2019.09.02.2] - 02-09-2019
### Agregado
 - Se agrega función para registrar pagos desde ivr
 
## [2019.09.02.1] - 02-09-2019
### Agregado
 - Se agrega boton de enviar comprobante (para pagos) por email en vista de cuenta de cliente.

## [2019.09.02.0] - 02-09-2019
### Agregado
 - Se incluye botón para forzar conexion en partial de _customer-contracts.

## [2019.08.30.1] - 30-08-2019
### Agregado
 - Se incluye botón para enviar recibo de pago por email desde index de pagos y vista de cada uno.
 - Se incluye bloque de info de contrato en vista de cuenta corriente del cliente.
 - Se incluye código de pago en vista de cuenta corriente del cliente.
 - Se agregan los campos created_at y created_by en la tabla payment_plan para determinar cuándo y quien creó el plan de pago. 
 
### Modificado
 - Se autocompleta la fecha de hoy en el modal de forzar conexion.
 - Cambio de lugar "Deudores" en menu.
 - Se modifica la forma en la que se aplican los pagos de proveedores a los comprobantes de proveedor, para evitar que se haga un doble asiento en las cuentas contables.

## [2019.08.30.0] - 30-08-2019
### Modificado
 - Se incluyen phone2, phone3 y phone4 en exportación de clientes deudores.
 
## [2019.08.29.2] - 29-08-2019
### Cambiado
 - Solicitud de Federico de Iperfex al devolver clientes sin pagos registrados

## [2019.08.29.1] - 29-08-2019
### Modificado
 - Cambios para creación automática de tickets de fibra.
 
## [2019.08.29.0] - 29-08-2019
### Agregado
 - Comando de consola para que actualice una categoria de tickets con una de mesa, y de ser necesario, cree una nueva.
 
## [2019.08.28.2] - 28-08-2019
### Cambiado
 - Correción de error en cuenta corriente de cliente, se muestra codigo html.
 

## [2019.08.28.1] - 28-08-2019
### Cambiado
 - Correccion error IVR con clientes Free
 
## [2019.08.28.0] - 28-08-2019
### Cambiado
 - Corrección error reportado Florencia Muñoz al crear contrato con domicilio distinto al del cliente

## [2019.08.27.0] - 27-08-2019
### Agregado
 - Se comienza a utilizar changelog
 - Versionado basado en la fecha del cambio, con formato año-mes-dia-versión_diaria.
### Cambiado
 - [APP] Saldo de la cuenta que se devuelve sin decimales, truncado al entero.
