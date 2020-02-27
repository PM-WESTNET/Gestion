# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
## [Pendiente de aprobación]
 
 - Cheques: Marcar como entregado al crear un pago a proveedor [entregaCheque]
 - Activación de Extensiones de pago: Tarea cron para asegurarse de que no queden extensiones en borrador [activacionExtensiones]
 - Correcion error duplicado de tickets en panel de cobranza
 - Correción filtros asignación masiva de planes
 - Asignacion de planes a clientes: Correción filtros asignación masiva de planes
 - Vista de ticket: Se modifica botón "Crear factura" para que solo muestre los tipos de comprobantes habilitados para
 la condición del cliente.
 - Alta de cliente: En desplegable "Cómo conoció a westnet?" se quitan las opciones de pasacalle y revista. Se agregan 
 las opciones de gigantografia, pantalla led e instagram [AD-9]
 - Reporte: Se agrega reporte de cantidad de clientes por medio de publicidad. [GES-645]
 - Agregado filtro de clientes con app instalada o no en Notificaciones [GES-682]
 - Se agrega opcion "Folleto" en selector de canales de publicidad en el alta del cliente. [GES-801]
 - Manejo de canales de publicidad: Alta, modificación, eliminación y deshabilitación [GES-645] Solicitado por Mariela
 - Reporte de clientes por canal de publicidad: Se agrega un gráfico comparativo y acumulativo de los canales de publicidad [GES-645] Solicitado por Mariela
 - Vista de tareas: Se agrega link a la vista del cliente de la tarea [AD-4]
 - Backups: Alerta a email si falla un backup [GES-810]
 -  Cierre de tickets de cobranza al cerrar un comprobante y dejar la deuda en cero [GES-773] Solicitado por Camila.
 
 ## [2020.02.26.0]
 ### Modificado
  - Descuentos por recomendado: Modifcación de función que calcula si el cliente tiene la primera factura pagada [GES-632/GES-673] Solicitado y aprobado por Camila
  - Alta de cliente: Celular 2 no requerido [GES-788] Solicitado y aprobado por Camila.
 - Se corrige dni con _ al final, en clientes con dni de 7 digitos [GES-789] Solicitado y aprobado por Camila.
 - Correccion de Reportes de clientes actualizados, extensiones de pago, e informe de pago [AD-2] Solicitado y aprobado por Maria Laura.
 ### Agregado
   - Tickets de instalaciones: Se agrega funcionalidad para cerrar tickets por período [AD-3] Solicitado y aprobado por Camila.
 
 ## [2020.02.21.0]
 ### Modificación
  - Corrección de visualización de reportes de Extensiones de pago, informe de pago y Actualización de cliente [AD-2]. Solicitado y Aprobado por Ma 
 
 ## [2020.02.19.0]
 ### Agregado
  - Notificación por email: Se agrega referencia a código de cliente - [GES-772] Solicitado y aprobado por Camila.
  - Categorias de Empleado [CON-14] Solicitado y aprobado por Marian.
  - Agregado categoria, fecah de alta, fecha de baja y observaciones a Empleado [CON-14]  Solicitado y aprobado por Marian.
 
 ## [2020.02.07.0]
 ### Agregado
 - Agregado Modulo de Empleados [CON-1]
 - Reporte de Tickets Cerrados por usuario [GES-92] 
 
 ### Modificado
 - Se corrige dni con _ al final, en clientes con dni de 7 digitos [GES-789]
 
 ## [2020.02.05.0]
 ### Agregado 
 - Se agrega reporte de Clientes por Nodo
 
 ## [2020.02.04.0]
 ### Agregado
 - Se ocultan categorías de tickets para optimizar los tiempos de creación de tickets. [GES-485] Solicitado y aprobado por Camila.
 
 ## [2020.02.03.0]
 ### Modificado
 - Contrato: Boton "Actualizar en ISP" agregado a la vista para impactar el contrato en wispro [GES-543] Solicitado y aprobado por Joaquin
 - Implementacion de mutex para evitar que el proceso de actualización de conexiones [GES-710] Aprobado por Camila
 - Exportacion de notificaciones: Se modifica la exportación y envio de notificaciones por SMS para qwe no tenga en 
  cuenta los teléfonos fijos. [GES-726] Solicitado y aprobado por Camila
 - Cron Activacion Adicional de Extension de pago: Se corrige bug al buscar contratos [GES-654] Solicitado y aprobado por Camila
 - Liquidación de vendedor: se optimiza búsqueda items de contrato asociados al vendedor [GES-786] Solicitado por Camila y aprobado por Maria Laura
 
 ## [2020.01.29.0]
 ### Modificado
  - Mobile App: Corrección de orden de comprobantes, del más nuevo al más viejo [GES-746] Solicitado y aprobado por Camila
  - Vista de cuenta monetaria: Debe, haber y saldo afectado por los filtros. [GES-688] Solicitado y aprobado por Marian
  - Api de clientes morosos y cortados: Solo devuelve clientes en estado activo, con contrato activo y conexion habilitada [GES-775] Solicitado y aprobado por Joaquin
   
  ## [2020.01.28.0]
  ### Agregado
  - Libro Mayor [GES-783]
  
  ## [2020.01.22.1]
  ### Agregado
  - Reporte de clientes actualizados [GES-764]
  - Alta de cliente: Agregado campo observaciones [GES-626]
  
  ### Modificado
  - Campos celular 1 y 2 obligatorios para nuevos clientes [GES-764]
  
  ## [2020.01.22.0]
  ### Agregado 
   - API para portal captivo: Se agrega el reemplazo del contenido de las notificaciones activas de explorador para cada
   uno de los clientes. [Ticket GES-568] Solicitado y aprobado por Joaquin
     
 ## [2020.01.16.0]
 ### Modificado
 - Vista de cuenta monetaria: Debe, haber y saldo afectado por los filtros. [GES-688] Solicitado y aprobado por Marian
 
 ## [2020.01.15.0]
 ### Modificado
  - Cierre de Lote de Ecopago: Se corrige error al crear la comision del ecopago [GES-574] Solicitado y aprobado por Elizabeth
  - Notificaciones por Explorador: Se añaden etiquetas reemplazables en el contenido de la notificación (Saldo, nombre
   de cliente etc). [Ticket GES-568] Solicitado y aprobado por Joaquin
  - Asignación masiva de planes a clientes: Se corrige la barra que indica el progreso del proceso [Ticket GES-557] Solicitado y aprobado por Camila
  - Extension de pago desde app: Correccion error que no permitia forzar a clientes con 1 factura adeudada [Ticket GES-620] Solicitado y aprobado por Camila
  - Notificaciones por email: envio en segundo plano [GES-614] Solicitado y aprobado por Camila
  - Registro de errores al crear movimientos desde pagos de proveedores [GES-648] [GES-669] Solicitado y aprobado por Marian
  - Al cambiar el cliente de un pago no se duplica movimiento [GES-650] Solicitado y aprobado por Marian
 
 ### Agregado
  - Pagos a proveedores: Se agrega fecha de creación en vista [GES-641] Solicitado por Marian y aprobado por Elizabeth
  - Notificaciones masiva a la app [GES-665] Solicitado por Elizabeth y aprobado por Camila
  - Se agrega rol con permisos para poder configurar la empresa de facturacion que se le asignará a los clientes nuevos [GES-663] Solicitado y aprobado por Camila   
  
 ## [2020.01.14.0]
 ### Modificado
  - Eliminación de telefonos duplicados por cliente
  - Limitacion al crear un cliente: Los telefonos no pueden estar repetidos
  
 ## [2020.01.13.0]
 ### Agregado
  - IVR: Se agrega indicador si el cliente es nuevo o no
  - IVR: Se agrega indicador para identificar a los clientes que se les debe verificar los datos, y ademas
   la fecha de la ultima actualizacion de los mismos
  - Se agrega endpoint para IVR get-customer-by-document-number.
  - API IVR: se agrega campo en customer que indica si el cliente tiene un contrato en estado de baja.
 ### Modificado
  - IVR: Correcion al error que definia la fecha de vencimiento de la extension de pago igual a la fecha que se le notificaba al cliente.
  - IVR: Se valida que el cliente no este de baja y tenga contratos activos. 
  - IVR : Cambio en salida de endpoint get-customer.

 ## [2020.01.07.1]
 ### Modificado
  - Fecha de nacimiento: se requiere solo cuando no es empresa
  - Notificaciones: Se agregan columnas DNI, email y email secundario a la exportacion de infobip   
 
 
 ## [2020.01.07.0]
 ### Modificado
  - Cambios de velocidad programada: Se limita el selector de nuevo plan a que muestre planes de fibra o WIFI dependiendo
   del plan actual del cliente.
  - Activacion de items de extension de pago automáticas: Se modifica cron para que tenga en cuenta conexiones en estado 
  activo, y no solo las forzadas.

 ## [2020.01.06.0]
 ### Agregado
 - Alta cliente: se agrega fecha de nacimiento
 
 ## [2020.01.02.0]
 ### Agregado
  - API IVR: se agrega campo que indique si el contrato es de fibra o no.
  
 ## [2019.12.30.1]
   ### Modificado
   - Movimientos: Se agrega rol de usuario el cual es el unico permitido a modificar y eliminar movimiento. Los movimientos se 
   pueden modificar o eliminar si no hay movimientos posteriores en estado cerrado.
   
 ## [2019.12.30.0]
 ### Modificado
  - Pagos a proveedores: Se corrige error que no permite cargar mas de un cheque a un mismo pago.
   
 ## [2019.12.27.0]
 ## Modificado
  - Alta Cliente: validación de número de documento solo permitir números, limitar de 7 a 8 caracteres cuando no es CUIT
  - Alta Cliente: validación de número de documento no puede ser del tipo 0000 o 9999 
  - Alta Cliente: validación de número de documento no puede empezar con 0 
  - Alta Cliente: validación de telefonos, solo permite ingresar numeros, se limita a  10 caracteres 
 
 ## [2019.12.26.0] 
 ## Agregado
  - Reportes: Grafico de torta extensiones de pago.
  - Reportes: Grafico de línea extensiones de pago.
  - Reportes: Grafico de torta informes de pago.
  - Reportes: Grafico de línea informes de pago.
 
 ## [2019.12.23.1]
  ### Agregado
  - Al iniciar proceso de baja se genera nota de crédito por el total de la deuda del cliente
   
 ## [2019.12.23.0]
 ### Modificado
  - Comprobantes a proveedores: Se modifica vista de pago "Aplicar a comprobante" para que permita aplicar el mismo comprobante
  a diferentes pagos.
 
 ## [2019.12.10.0]
 ### Agregado
 - Se agrega proceso de facturación por lotes en segundo plano
 - Se configura cron para que el proceso de facturación por lotes se levante automáticamente cuando por una razón 
 externa ha sido interrumpido.
 ### Modificado
 - Se modifica el proceso de facturación por lotes de manera que no bloquee las tareas en el sistema al usuario que 
 inicia el proceso.
 
 ## [2019.11.15.0]
 - IVR: Se modifica el formato del nombre del cliente en la respuesta de endpoint
 
 ## [2019.11.14.1]
 ### Modificado
 - Pagos de clientes: Se registra el usuario que ha cargado un pago por transferencia
 

 ## [2019.11.14.0]
 ### Agregado
  - Creación de cliente nuevo: Se agrega la posibilidad de adjuntar imagenes del documento e impuesto en el momento de 
  crear un nuevo cliente.
  - Index de clientes: Se agregan columnas para identificar rápidamente aquellos clientes que no poseen foto del documento
  o de un impuesto cargada.
  - Index de clientes: Se agrega enlace para que al hacer click en los iconos que indican si tiene imagen de documento o
  imagen de impuesto lleven a la vista del cliente o a la actualización del cliente.
  
 ## [2019.11.13.0]
 ### Modificado
  - Asignación masiva de planes a clientes: Se corrige la barra que indica el progreso del proceso.
  - Asignacion masiva de planes a clientes: Se corrige error que ocurria en el servidor con ciertos contratos.
  - Cierre de facturas: Se corrige error que se generaba al cerrar una factura con un descuento fijo aplicado al cliente.
  - Cierre de comprobantes: Cambios en xml enviado a AFIP cuando el comprobante tiene descuentos fijos.
  - IVR: Se limita la posibilidad de crear extensiones de pago solo cuando no son clientes nuevos y han pagado su primera factura.
  - IVR: Se agrega numeros de telefono a la info del cliente.
  - IVR: Al no poder crear extension de pago se indica si el motivo es por morosidad o no
   
 
 ## [2019.11.08.0]
 ### Modificado
  - Panel de tickets de cobranza: Se arregla error que se presentaba al aplicar el filtro de estado "No responde".
  - Pagos a proveedores: Se corrige error que se generaba al agregar el segundo item de un pago con diferente medio de
  pago al primer item.
  - Panel de tickets: Correcion de conteo de tickets.
  - Liquidacion de Vendedor: Se corrige error al crear factura de proveedor.
  
 
 ## [2019.11.07.0]
 ### Agregado
 - Notificaciones por explorador: Se agrega comando de cron que guarda los clientes a los que se debe notificar.
 - Notificaciones por explorador: Se agrega API para que el portal consulte los clientes que se tienen en cuenta en las 
 notificaciones por explorador.
 
 ## [2019.11.06.0]
 ### Modificado
 - Informar Pago: Error al enviar foto capturada por la camara
 - Actualizacion de app en android con ultimos cambios
 
 ## [2019.11.05.0] - 05-11-2019
 ### Modificado
  - Comprobantes a proveedores: Se corrige error que se generaba al crear un comprobante nuevo.

 ##[2019.11.01.0] - 01-11-2019
 ### Modificado
 - Vista de movimientos de cuentas monetarias: Se corrige error que se generaba al tildar un movimiento como verificado.
 
 ## [2019.10.31.2] - 31-10-2019
 ### Modificado
 - Pagos a proveedores: Se limita la eliminación de pagos a proveedores.
 - Pagos a proveedores: Se limita la eliminación de comprobantes a proveedores.
 - Pagos a proveedores: Se agrega botón par aguardar pagos a proveedor en estado borrador.
 - Pagos a proveedores: Se agrega el estado del pago en la vista del mismo.
 - Pagos a proveedores: Se agrega el estado del pago en el listado de pagos a proveedor.
 
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
