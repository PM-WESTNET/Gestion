# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
## [Pendiente de aprobación]
 - Pagos a proveedores: Se limita la eliminación de pagos a proveedores.
 - Pagos a proveedores: Se limita la eliminación de comprobantes a proveedores.
 - Pagos a proveedores: Se agrega botón par aguardar pagos a proveedor en estado borrador.
 - Pagos a proveedores: Se agrega el estado del pago en la vista del mismo.
 - Pagos a proveedores: Se agrega el estado del pago en el listado de pagos a proveedor.
 - Cheques: Marcar como entregado al crear un pago a proveedor [entregaCheque]
 - Activación de Extensiones de pago: Tarea cron para asegurarse de que no queden extensiones en borrador [activacionExtensiones]
 - Informar Pago: Error al enviar foto capturada por la camara [informesPagosDuplicados]
 - Reporte de Tickets Cerrados por usuario [reporteCierreTickets]
 - Panel de tickets de cobranza: Se arregla error que se presentaba al aplicar el filtro de estado "No responde". 30-10-2019
 - Pagos a proveedores: Se corrige error que se generaba al agregar el segundo item de un pago con diferente medio de pago al primer item
 
 ## [2019.10.31.1] - 31-10-2019
 ### Modificado
  - Cambio de Velocidad Programado: Solo se muestran contratos activos
  - Cambio de Velocidad Programado: Solo se muestran planes
  
 ## [2019.10.31.0] - 31-10-2019
 ### Agregado
 - Resumenes bancarios: Importación de resumenes bancarios.
 - Conciliaciones automática de cuentas.
 - Contabilidad: Asignación de tiopos de operaciones y códigos a entidades bancarias.
 - IVR: Habilitación de cliente 27237 para pruebas de IVR
 ### Modificado
  - Contabilidad:
    - Cierre de movimientos contables: Al cerrar un movimiento de una cuenta monetaria, se cierran todos los movimientos
    anteriores a ese.
    - Limitación al eliminar o actualizar pagos a proveedor, Comprobantes a proveedor, Cheques, Comprobantes o pagos de 
    clientes  cuando sus movimientos contables correspondientes ya están cerrados para evitar diferencias en las cajas.
    - Los movimientos generados automáticamente a partir de un pago a proveedor por ejemplo, se hacen con la fecha actual
    y no con la fecha del pago. Esto evita movientos en períodos en los cuales ya se han verificado todos los movimientos.
  - Pagos a proveedores:
    - Se limita la creación de los pago a proveedores a X dias para atrás desde el dia actual. La cantidad de dias es 
    configurable.
  
## [2019.10.28.0] - 28-10-2019
### Modificado
 - Optimizacion de widget de notificaciones

## [2019.10.25.0] - 25-10-2019
### Modificado
 - Correccion de número de Servicio Técnico mostrado en la app

## [2019.10.23.0] - 23-10-2019
### Agregado
 Cambios de velocidad programados:
   - Se agregan botones en el contrato y vista de cliente para crear un cambio de velocidad programado.
   - Se agrega panel de cambios de velocidad programados, con posibilidad de filtrar por cliente, plan, fecha, usuario que lo creó, y si es un cambio aplicado o no.
   - Se crea commando que aplica los cambios de velocidad.
   
## [2019.10.22.0] - 22-10-2019
### Modificado
 - Se limita la eliminación de pagos a proveedores.
 - Se limita la eliminación de comprobantes a proveedores

## [2019.10.21.0] - 21-10-2019
### Modificado
 - Panel de cuenta corriente de provedor: Se modifica el formato en el que se muestra columna Saldo en listado de comprobantes.

## [2019.10.18.0] - 18-10-2019
### Modificado
 - Panel de informes de pago de clientes: Se agrega la posibilidad en filtros del panel de poder seleccionar todos los medios de pago.

## [2019.10.17.0] - 17-10-2019
### Agregado
 - Solicitud de extensión de pago desde IVR: Se registra un historial al crear la extensión de pago.
 - Solicitud de extensión de pago desde APP: Se registra un historial al crear la extensión de pago.
 - Reporte de extensiones de pago solicitadas desde IVR y APP.

## [2019.10.15.0] - 15-10-2019
### Modificado
 - IVR: Se corrige forzado de conexion, ahora si el cliente debe más de 1 factura no puede forzar la conexión, al igual que los clientes nuevos.
 - IVR:  Al informar pago solo se solicita el monto abonado.
 - IVR: Solo se envian facturas A y B por email.
 - Correccion al importar pago de pago facil de cliente sin empresa asignada.

## [2019.10.10.1] - 10-10-2019
### Modificado
 - Exportación de notificaciones: Se cambia la exportación de los clientes que se incluyen en la notificación a un excel.
 
## [2019.10.10.0] - 10-10-2019
### Agregado
 - Cierre de tickets de cobranza automáticos: Al realizar un pago un cliente, si su deuda es menor al 
 tolerante y si no tiene gestiones, se pasa el ticket a estado "Pagó sin gestionar", si tiene una o mas 
 gestiones se cambia el estado a "Pagó"
### Modificado
 - Reporte de tickets: el selector de estados, se carga de acuerdo a la categoría seleccionada.

## [2019.10.09.1] - 09-10-2019
### Agregado
 - Panel de Tickets de instalación: Se agrega columna que permite marcar tickets como "descontados".

## [2019.10.09.0] - 09-10-2019
### Agregado
 - PDF de comprobante: Se agrega importe total de descuento.
 - Creación o actualización de descuentos: Se genera un alerta cuando mas de un descuento por recomendado está activo.
 - Descuentos: Posibilidad de marcar un descuento como "persistente".
 - Descuento por recomendado persistente: Modificaciones necesarias para que los descuentos persistentes, solo se inhabiliten cuando se haya aplicado.
 - Descuento por recomendado persistente del 25%
 
## [2019.10.07.0] - 07-10-2019
### Modificado
 - Exportacion de libro IVA ventas: Se modifica consulta para optimizar tiempos de espera. 

## [2019.10.03.0] - 03-10-2019
### Modificado
 - Modificación del proceso de creación de extensión de pago para IVR solicitado por Laura Mineo
 
## [2019.10.02.0] - 02-10-2019
### Modificado
 - Se modifica la forma en la que se calcula el neto de los comprobantes cuando se les aplica al total un descuento fijo.
 - Se modifica PDF de libro de IVA compras y ventas.
 - Se modifica Excel de libro de IVA compras y ventas.

## [2019.10.01.0]
### Modificado
 - Se corrige la generación en blanco del PDF del libro de compras.

## [2019.09.30.0]
### Agregado
 - Se agregan items de configuración para telefonos de Atención general mostrados en la APP.
 - Se modifica API de APP para indicar a la app si son con WhatsApp o no.
### Modificado
 - Se cambia redireccion al hacer click en tarea desde la agenda a la vista de la tarea.
 - Se definen filtros por defecto en  vista de agenda.

## [2019.09.26.0] - 26-09-2019
### Modificado
 - Se modifica código que permite la eliminación de tickets
 - Se modifica código del modal para forzado de conexion en cuenta corriente del cliente.
 
## [2019.09.23.1] - 23-09-2019
### Modificado
 - Se modifica la respuesta de la api de APP. Mostraba "Su cuenta presenta deuda" al tener saldo positivo.
 
## [2019.09.23.0] - 23-09-2019
### Modificado
 - Limitación de borrado de tareas cuando están asociadas a un ticket.
 
## [2019.09.20.1] - 20-09-2019
### Modificado
 - Filtros de estado de emails e instalacion de app agregados a la exportacion de clientes
 
## [2019.09.20.0] - 20-09-2019
### Modificado
 - Al filtrar por estados de emails para validar en elastic email, no se tienen en cuenta los
 clientes con emails vacios.
 
## [2019.09.19.0] - 19-09-2019
### Modificado
 - Correccion de fecha de tarea de ticket en panel de tickets de Instalaciones realizadas.
 - Agregado de fecha de tarea de ticket en panel de tickets de cobranza
 
## [2019.09.17.2]
 - Opcion de mostrar los tickets de todos los usuarios en los paneles de tickets de cobranza e instalaciones 
 - Modificaciones en index de descuento a cliente.
 
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
