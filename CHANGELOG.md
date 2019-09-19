# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
## [2019.09.19.0] - 19-09-2019
### Modificado
 - Correccion de fecha de tarea de ticket en panel de tickets de Instalaciones realizadas.
 - Agregado de fecha de tarea de ticket en panel de tickets de cobranza
 
## [2019.09.17.2]
 - Opcion de mostrar los tickets de todos los usuarios en los paneles de tickets de cobranza e instalaciones 

## [2019.09.17.3] - 17-09-2019
### Modificado
 - Se modifica cron para actualizar saldo de los clientes.
 
## [2019.09.17.1] - 17-09-2019
### Modificado
 - Se modifican las columnas en panel de gestion de instalaciones.
 - Se quitan restricciones por usuario para ver tickets de otros en panel de cobranza y panel de gestion de instalaciones.
 
## [2019.09.17.0] - 17-09-2019
### Modificado
 - Se cambia medio de filtrado en panel de tickets de instalaciones.
 - Se cambia medio de filtrado en panel de tickets de gestion de cobraza.

## [2019.09.16.1] - 16-09-2019
### Modificado
 - Mejora de usabilidad: Cambio en el selector de descuento al crear un descuento al cliente para que permita buscar con autocompletado.
 - Corrección de error en vista de estado de ticket.
 - Corrección de error al guardar tipo de acción - Tickets.
 

## [2019.09.16.0] - 16-09-2019
### Agregado
 - Al activar o desactivar un contrato, el estado del cliente cambia de Activo a Inactivo.
 
## [2019.09.13.2] - 13-09-2019
### Modificado 
 - Eliminación de ticket implica la eliminación de las gestiones.

## [2019.09.13.1] - 13-09-2019
### Modificado
 - Validación de combinación numero de comprobante y proveedor cambiada por numero + proveedor + tipo de comprobante en comprobantes.
 
## [2019.09.13.0] - 13-09-2019
### Agregado
 - Las gestiones de ticket se vinculan siempre a una observación.
### Modificado
 - Cambios en modal de observaciones, para el registro de gestiones de tickets.

## [2019.09.12.1] - 12-09-2019
### Agregado
 - Reporte de Tickets.
### Modificado
 - Se elimina información de ultimo comprobante en endpoint de IVR (get-customer).

## [2019.09.12.0] - 12-09-2019
### Modificado
 - Se cambia el schema de estados listados en filtro de panel de instalaciones.
 
## [2019.09.11.0] - 11-09-2019
### Agregado
 - Se agrega información de la última factura en endpoint de IVR (customer/get-customer).
### Modificado
 - Se anula el tiempo máximo de exportación en notificación de SMSInfobip.
 
## [2019.09.10.1] - 10-09-2019
### Agregado
 - Se agrega alerta para usuarios que compartan el rol User-alert-new-no-verified-tranferences cuando existan informes de pago que no estén marcados como verificados.
 - Se agrega boton en index de informes de pago para poder marcar como verificados aquellos que tengan medio de pago transferencia.
 - Se agrega botonn en vista de informe de pago para poder marcar como verificado.
### Modificado
 - Se modifican los filtros de panel de tickets de instalaciones.
 - Se modifican los filtros de panel de tickets de gestión de cobranza.

## [2019.09.10.0] - 10-09-2019
### Modificado
 - Se quitan los clientes con contrato en estado de baja del listado del panel de cobranza.
 
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
