<div class="help">

    <h1><?= Yii::t('help', 'Navigation') ?></h1>

    <p>Para navegar por las distintas secciones del sistema puede utilizar la barra
        ubicada en la parte superior de la pantalla, o utilizar el menú provisto por
        la pantalla de inicio del Sistema. Para ingresar a una sección simplemente
        haga clic sobre el ítem correspondiente.
    </p>
    <p>Una vez dentro de una sección, un menu que se encuentra bajo el
        título de la sección le permitirá navegar por las funcionalidades relacionadas.
    </p>
    <p>La barra superior se encuentra en todas las pantallas de la aplicación
        y siempre será visible.
    </p>
    <p>El menu de navegación de la pantalla principal del Sistema cuenta con un
        código de colores que le permitirá asociar rápidamente las disntintas
        funcionalidades del sistema con sus colores correspondientes, para brindar una
        mejor experiencia de usuario.
    </p>

    <?php if (\webvimark\modules\UserManagement\models\User::hasRole('Admin')) { ?>
    <h2>Facturación</h2>
    <p>En esta sección encontrará toda la información necesaria para realizar las facturas de su empresa.</p>
    <h3>Factura A</h3>
    <p>Desde este ítem se generan todas las Facturas A de la empresa. Para ello se deben completar todos los campos de
        una factura:</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la factura. Cada cliente por defecto tiene asignado una empresa de facturación,por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la factura. Para ello se debe incluir cualquier campo del
        cliente (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la
        búsqueda de acuerdo al parámetro ingresado.</p>
    <ul>
        <li>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</li>
        <li>Si el cliente no existe, hacer click en Alta de Cliente</li>
    </ul>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la Factura.</p>
    <h4>Buscar producto</h4>
    <p>Para buscar un producto, se debe ingresar el producto o servicio a facturar y luego presionar Enter.</p>
    <p>Si no se introduce ningún valor y se presiona Enter, se obtiene el listado de todos los productos/servicios
        disponibles para facturar.</p>
    <p>Al presionar agregar sobre el producto/servicio deseado, se lo agrega a la factura.</p>
    <h4>Productos a facturar</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar una factura</h4>
    <p><strong>Aceptar:</strong> cierra la factura y la misma queda pendiente de pago. Se informa a AFIP y si el proceso
        es correcto, se informa el CAE.</p>
    <p><strong>Aceptar y pagar:</strong> cierra la factura y se habilitan las opciones para realizar el pago de la
        factura.</p>
    <p><strong>Guardar Borrador:</strong> la factura queda guardada en borrador y puede ser editada cuando se lo desee.
    </p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la factura.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una factura de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello
        se debe detallar concepto (nombre del ítem a facturar), cantidad y precio neto por unidad.</p>
    <h3>Factura B</h3>
    <p>Desde este ítem se generan todas las Facturas B de la empresa. Para ello se deben completar todos los campos de
        una factura:</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la factura. Cada cliente por defecto tiene asignado una empresa de facturación,por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la factura. Para ello se debe incluir cualquier campo del
        cliente (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la
        búsqueda de acuerdo al parámetro ingresado.</p>
    <ul>
        <li>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</li>
        <li>Si el cliente no existe, hacer click en Alta de Cliente</li>
    </ul>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la Factura.</p>
    <h4>Buscar producto</h4>
    <p>Para buscar un producto, se debe ingresar el producto o servicio a facturar y luego presionar Enter.</p>
    <p>Si no se introduce ningún valor y se presiona Enter, se obtiene el listado de todos los productos/servicios
        disponibles para facturar.</p>
    <p>Al presionar agregar sobre el producto/servicio deseado, se lo agrega a la factura.</p>
    <h4>Productos a facturar</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar una factura</h4>
    <p><strong>Aceptar:</strong> cierra la factura y la misma queda pendiente de pago. Se informa a AFIP y si el proceso
        es correcto, se informa el CAE.</p>
    <p><strong>Aceptar y pagar:</strong> cierra la factura y se habilitan las opciones para realizar el pago de la
        factura.</p>
    <p><strong>Guardar Borrador:</strong> la factura queda guardada en borrador y puede ser editada cuando se lo desee.
    </p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la factura.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una factura de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello
        se debe detallar concepto (nombre del ítem a facturar), cantidad y precio neto por unidad.</p>
    <h3>Factura X</h3>
    <p>Desde este ítem se generan todas las Facturas X de la empresa. Para ello se deben completar todos los campos de
        una factura:</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la factura. Cada cliente por defecto tiene asignado una empresa de facturación,por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la factura. Para ello se debe incluir cualquier campo del
        cliente (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la
        búsqueda de acuerdo al parámetro ingresado.</p>
    <ul>
        <li>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</li>
        <li>Si el cliente no existe, hacer click en Alta de Cliente</li>
    </ul>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la Factura.</p>
    <h4>Buscar producto</h4>
    <p>Para buscar un producto, se debe ingresar el producto o servicio a facturar y luego presionar Enter.</p>
    <p>Si no se introduce ningún valor y se presiona Enter, se obtiene el listado de todos los productos/servicios
        disponibles para facturar.</p>
    <p>Al presionar agregar sobre el producto/servicio deseado, se lo agrega a la factura.</p>
    <h4>Productos a facturar</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar una factura</h4>
    <p><strong>Aceptar:</strong> cierra la factura y la misma queda pendiente de pago. </p>
    <p><strong>Guardar Borrador:</strong> la factura queda guardada en borrador y puede ser editada cuando se lo desee.
    </p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la factura.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una factura de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello
        se debe detallar concepto (nombre del ítem a facturar), cantidad y precio neto por unidad.</p>
    <h3>Nota de Crédito A</h3>
    <p>Desde este ítem se generan todas las Notas de Crédito A de la empresa. Para ello se deben completar todos los
        campos de la Nota de Crédito (NC).</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la NC. Cada cliente por defecto tiene asignado una empresa de facturación,<br/>por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la NC. Para ello se debe incluir cualquier campo del cliente
        (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la búsqueda de
        acuerdo al parámetro ingresado.</p>
    <p>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</p>
    <p>Si el cliente no existe, hacer click en Alta de Cliente</p>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la NC.</p>
    <h4>Buscar producto</h4>
    <p>Ingresar el producto o servicio a facturar y luego presionar Enter. Si no se introduce ningún valor y se presiona
        Enter, se obtiene el listado de todos los productos/servicios disponibles.<br/>Presionar Agregar sobre el
        producto/servicio deseado para agregarlo a la NC.</p>
    <h4>Detalle de productos incluidos</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar Nota de Crédito</h4>
    <p>Aceptar: cierra la NC y se informa a AFIP. Si el proceso es correcto, se informa el CAE.</p>
    <p>Guardar Borrador: la NC queda guardada en borrador y puede ser editada cuando se lo desee.</p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la NC.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una NC de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello se
        debe detallar concepto (nombre del ítem), cantidad y precio neto por unidad.</p>
    <h3>Nota de Crédito B</h3>
    <p>Desde este ítem se generan todas las Notas de Crédito B de la empresa. Para ello se deben completar todos los
        campos de la Nota de Crédito (NC).</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la NC. Cada cliente por defecto tiene asignado una empresa de facturación,<br/>por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la NC. Para ello se debe incluir cualquier campo del cliente
        (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la búsqueda de
        acuerdo al parámetro ingresado.</p>
    <p>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</p>
    <p>Si el cliente no existe, hacer click en Alta de Cliente</p>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la NC.</p>
    <h4>Buscar producto</h4>
    <p>Ingresar el producto o servicio a facturar y luego presionar Enter. Si no se introduce ningún valor y se presiona
        Enter, se obtiene el listado de todos los productos/servicios disponibles.<br/>Presionar Agregar sobre el
        producto/servicio deseado para agregarlo a la NC.</p>
    <h4>Detalle de productos incluidos</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar Nota de Crédito</h4>
    <p><strong>Aceptar:</strong> cierra la NC y se informa a AFIP. Si el proceso es correcto, se informa el CAE.</p>
    <p><strong>Guardar Borrador:</strong> la NC queda guardada en borrador y puede ser editada cuando se lo desee.</p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la NC.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una NC de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello se
        debe detallar concepto (nombre del ítem), cantidad y precio neto por unidad.</p>
    <h3>Nota de Crédito X</h3>
    <p>Desde este ítem se generan todas las Notas de Crédito X de la empresa. Para ello se deben completar todos los
        campos de la Nota de Crédito (NC).</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la NC. Cada cliente por defecto tiene asignado una empresa de facturación,<br/>por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la NC. Para ello se debe incluir cualquier campo del cliente
        (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la búsqueda de
        acuerdo al parámetro ingresado.</p>
    <p>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</p>
    <p>Si el cliente no existe, hacer click en Alta de Cliente</p>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la NC.</p>
    <h4>Buscar producto</h4>
    <p>Ingresar el producto o servicio a facturar y luego presionar Enter. Si no se introduce ningún valor y se presiona
        Enter, se obtiene el listado de todos los productos/servicios disponibles.<br/>Presionar Agregar sobre el
        producto/servicio deseado para agregarlo a la NC.</p>
    <h4>Detalle de productos incluidos</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar Nota de Crédito</h4>
    <p><strong>Aceptar:</strong> cierra la NC y se asigna el valor total a la cuenta del cliente.</p>
    <p><strong>Guardar Borrador:</strong> la NC queda guardada en borrador y puede ser editada cuando se lo desee.</p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la NC.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una NC de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello se
        debe detallar concepto (nombre del ítem), cantidad y precio neto por unidad.</p>
    <h3>Nota de Débito A</h3>
    <p>Desde este ítem se generan todas las Notas de Débito A de la empresa. Para ello se deben completar todos los
        campos de la Nota de Débito (ND).</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la ND. Cada cliente por defecto tiene asignado una empresa de facturación,<br/>por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la ND. Para ello se debe incluir cualquier campo del cliente
        (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la búsqueda de
        acuerdo al parámetro ingresado.</p>
    <p>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</p>
    <p>Si el cliente no existe, hacer click en Alta de Cliente</p>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la NC.</p>
    <h4>Buscar producto</h4>
    <p>Ingresar el producto o servicio a facturar y luego presionar Enter. Si no se introduce ningún valor y se presiona
        Enter, se obtiene el listado de todos los productos/servicios disponibles.<br/>Presionar Agregar sobre el
        producto/servicio deseado para agregarlo a la NC.</p>
    <h4>Detalle de productos incluidos</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar Nota de Débito</h4>
    <p>Aceptar: cierra la ND y se informa a AFIP. Si el proceso es correcto, se informa el CAE.</p>
    <p>Guardar Borrador: la ND queda guardada en borrador y puede ser editada cuando se lo desee.</p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la NC.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una ND de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello se
        debe detallar concepto (nombre del ítem), cantidad y precio neto por unidad.</p>
    <h3>Nota de Débito B</h3>
    <p>Desde este ítem se generan todas las Notas de Débito B de la empresa. Para ello se deben completar todos los
        campos de la Nota de Débito (ND).</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la ND. Cada cliente por defecto tiene asignado una empresa de facturación,<br/>por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la ND. Para ello se debe incluir cualquier campo del cliente
        (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la búsqueda de
        acuerdo al parámetro ingresado.</p>
    <p>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</p>
    <p>Si el cliente no existe, hacer click en Alta de Cliente</p>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la NC.</p>
    <h4>Buscar producto</h4>
    <p>Ingresar el producto o servicio a facturar y luego presionar Enter. Si no se introduce ningún valor y se presiona
        Enter, se obtiene el listado de todos los productos/servicios disponibles.<br/>Presionar Agregar sobre el
        producto/servicio deseado para agregarlo a la NC.</p>
    <h4>Detalle de productos incluidos</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar Nota de Débito</h4>
    <p>Aceptar: cierra la ND y se informa a AFIP. Si el proceso es correcto, se informa el CAE.</p>
    <p>Guardar Borrador: la ND queda guardada en borrador y puede ser editada cuando se lo desee.</p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la NC.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una ND de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello se
        debe detallar concepto (nombre del ítem), cantidad y precio neto por unidad.</p>
    <h3>Nota de Débito X</h3>
    <p>Desde este ítem se generan todas las Notas de Débito X de la empresa. Para ello se deben completar todos los
        campos de la Nota de Débito (ND).</p>
    <h4>Empresa</h4>
    <p>Define la empresa que emitirá la ND. Cada cliente por defecto tiene asignado una empresa de facturación,<br/>por
        lo que al cargar el cliente este campo se actualiza automáticamente.</p>
    <h4>Cliente</h4>
    <p>Permite seleccionar el cliente al que se le emitirá la ND. Para ello se debe incluir cualquier campo del cliente
        (Nro, DNI, Nombre, Apellido, etc) y se debe hacer click o presionar Enter para que se efectué la búsqueda de
        acuerdo al parámetro ingresado.</p>
    <p>En el listado de resultados de búsqueda, hacer click en Seleccionar el resultado buscado.</p>
    <p>Si el cliente no existe, hacer click en Alta de Cliente</p>
    <h4>Observaciones</h4>
    <p>En este campo se deben cargar todas las observaciones que se quieran incluir en la ND.</p>
    <h4>Buscar producto</h4>
    <p>Ingresar el producto o servicio a facturar y luego presionar Enter. Si no se introduce ningún valor y se presiona
        Enter, se obtiene el listado de todos los productos/servicios disponibles.<br/>Presionar Agregar sobre el
        producto/servicio deseado para agregarlo a la ND.</p>
    <h4>Detalle de productos incluidos</h4>
    <ul>
        <li><strong>Cantidad: </strong>total de unidades a facturar.</li>
        <li><strong>Precio por unidad: </strong>precio por defecto del producto/servicio sin IVA. Para modificar el
            precio, introducir el nuevo monto y presionar Enter.
        </li>
        <li><strong>Total de Línea: </strong>resultado del cálculo entre los campos cantidad y precio por unidad (IVA
            incluido).
        </li>
    </ul>
    <h4>Cerrar Nota de Débito</h4>
    <p><strong>Aceptar:</strong> cierra la ND y se asigna el valor total a la cuenta del cliente.</p>
    <p><strong>Guardar Borrador:</strong> la ND queda guardada en borrador y puede ser editada cuando se lo desee.</p>
    <h4>Usuario</h4>
    <p>Permite la posibilidad de asignar que usuario emite la ND.</p>
    <h4>Detalle Manual</h4>
    <p>Permite emitir una ND de un producto/servicio que no se encuentra incorporado en el sistema.<br/>Para ello se
        debe detallar concepto (nombre del ítem), cantidad y precio neto por unidad.</p>
    <h3>Facturación Por Lotes</h3>
    <p>Ese proceso permite la generación de facturas por lotes para un período determinado de tiempo. Antes de realizar
        este proceso se define a quién se desea facturar. Para ello se aplican filtros de búsqueda (incluidos dentro del
        box Datos de Facturación).</p>
    <p><strong>Empresa: </strong>filtra los contratos de esa empresa en particular.</p>
    <p><strong>Tipo de comprobante: </strong>define qué tipo de comprobante se va a emitir. Depende de la empresa
        seleccionada los comprobantes habilitados a emitir.</p>
    <p><strong>Período: </strong>período que se desea facturar.</p>
    <p>Una vez definidos estos 3 parámetros se procede a <strong>Buscar contratos </strong>para
        luego <strong>Facturar.</strong></p>
    <p></p>
    <h3>Cerrar Facturas de lotes pendientes</h3>
    <p>Al realizar un proceso de facturación por lotes pueden existir problemas de comunicación con AFIP y algunas
        facturas no haberse emitido correctamente.</p>
    <p>Para cerrar las facturas pendientes se deben definir qué facturas se cerrarán, aplicando los filtros de búsqueda
        correspondiente.</p>
    <p><strong>Empresa: </strong>filtra los contratos de una empresa en particular.</p>
    <p><strong>Tipo de comprobante: </strong>define qué tipo de comprobante se va a emitir. Depende de la empresa
        seleccionada los comprobantes habilitados.</p>
    <p>Una vez que los filtros de búsqueda se definieron se procede a <strong>Buscar</strong> para
        luego <strong>Cerrar</strong> las facturas pendientes.</p>
    <h2>Vendedores</h2>
    <p>Esta funcionalidad permite a los vendedores generar una nueva venta. Para ello debe consultar previamente si el
        cliente, ya que puede haber tenido en algún momento el servicio y es necesario comprobar la existencia previa de
        datos. </p>
    <h3>Comprobar si existe el cliente</h3>
    <p>Para chequear si existe el cliente,</p>
    <ul>
        <li>En el campo <strong>Buscar cliente </strong>intoducir el Nombre del Cliente o su Nro de DNI.</li>
        <li>Presionar Enter o hacer click en buscar.</li>
    </ul>
    <p>Si el resultado de la búsqueda no es satisfactorio, se procede al Alta de un Nuevo Cliente.</p>
    <h3>Alta de Cliente</h3>
    <p>Para generar el alta de un nuevo cliente se deben completar los campos del formulario de Alta.</p>
    <ul>
        <li>Tipo de cliente: Seleccionar el tipo de relación que tiene con la AFIP el cliente a dar de alta. Este campo
            está directamente relacionado con el tipo de factura que se emitirá para el cliente.
        </li>
        <li>Datos personales del cliente.</li>
        <li>Referenciado por: si un cliente contrata la empresa por una sugerencia de un cliente activo, se debe aclarar
            para generar un descuento que genera el contacto.
        </li>
        <li>Formas de notificar: define cómo se le notificará al cliente las novedades de la empresa.</li>
        <li>Dirección: permite geolocalizar el lugar de la conexión del cliente. Primero se deben cargar todos los datos
            de la dirección y luego presionar <strong>Localizar en el mapa</strong>.
        </li>
    </ul>
    <p></p>
    <h2>Comprobantes</h2>
    <h3>Configuración</h3>
    <p>En esta sección tendremos acceso a configuración de aspectos contables del sitio. Se recomienda que los cambios
        sean realizados por personal que conozca todas las implicancias derivadas de realizar modificaciones. Todos los
        cambios que se realicen se verán reflejados en la contabilidad de las diferentes empresas de facturación.</p>
    <h4>Tipos de comprobantes</h4>
    <p>Desde este ítem se pueden gestionar todos los tipos de comprobantes con los que el sistema trabajará, por ejemplo
        Factura A, Factura B, Factura X, etc. Cada comprobante tiene sus parámetros de configuración propios que
        definirán su comportamiento en diferentes operaciones en el sistema.</p>
    <h5>Alta de tipo de comprobante</h5>
    <p>Para generar un nuevo comprobante se deben completar los siguientes campos:</p>
    <ul>
        <li>Nombre del comprobante. Ejemplo Factura A, Presupuesto, Orden de Pedido, etc.</li>
        <li>Código identificador del comprobante.</li>
        <li>¿Cliente requerido? Si está habilitada esta opción al emtiir el comprobante el campo cliente será
            obligatorio.
        </li>
        <li>Iniciador: si esta opción está habilitada indica que este comprobante puede generarse sin la necesidad de
            estar vinculado con otro comprobante. Por ejemplo a partir de un presupuesto se genera una orden de pedido.
            En ese caso el presupuesto tiene que tener activada esta opción.
        </li>
        <li>Puede generar: este campo informa con que otros documentos se puede vincular.</li>
        <li>Vista: si se elabora un template para el documento, puede ser determinado en este punto.</li>
        <li>Multiplicador: este campo determina el comportamiento contable del comprobante. </li>
        <li>Clase y Clase de Facturación E. son campos configurados por los desarrolladores del sistema para la correcta
            emisión de facturas electrónicas.
        </li>
    </ul>
    <h4>Unidades</h4>
    <p>El sistema permite trabajar con múltiples unidades de medición, cómo puede ser unidades, kg., mts. etc. Desde
        esta funcionalidad se crea una nueva unidad de medición y cuando se genera un nuevo producto / servicio se le
        indica al mismo con qué unidad debe ser medido.</p>
    <h4>Monedas</h4>
    <p>Esta funcionalidad permite la generación de las monedas con las que operará el sistema. </p>
    <h4>Impuestos</h4>
    <p>Este módulo contempla la generación de los impuestos municipales, provinciales y nacionales. </p>
    <h4>Tasas impositivas</h4>
    <p>Los impuestos anteriormente generados pueden tener diferentes tasas de aplicación. Por ejemplo IVA puede tener
        una tasa del 10,5%, 21% o 27%.</p>
    <p>Se debe generar una tasa impositiva por cada % de aplicación diferente. </p>
    <h4>Condiciones frente a IVA</h4>
    <p>Esta funcionalidad contempla los comprobantes que se deben emitir a un cliente teniendo en cuenta su condición
        frente a IVA. Por ejemplo si el cliente es un Consumidor Final debe recibir Factura B pero no A.</p>
    <p><strong>Descripción de campos</strong></p>
    <ul>
        <li>Nombre: nombre de la condición frente a IVA (Consumidor Final, Responsable Inscripto, etc.)</li>
        <li>Tipos de comprobantes: tipos de comprobantes que un cliente con esa condición puede recibir.</li>
        <li>Tipos de documentos: tipos de documentos válidos para ese documento (DNI, CUIT, etc.)</li>
        <li>Exento: indicar si la condición es exento de IVA.</li>
    </ul>
    <p><strong></strong><span>Medios de pago</span></p>
    <p>Esta funcionalidad permite la gestión integral de los medios de pagos habilitados en el sistema.</p>
    <p><strong>Descripción de campos</strong></p>
    <ul>
        <li>Nombre: nombre del medio de pago.</li>
        <li>Estado:
            <ul>
                <li>Disponible: se puede utilizar en el sistema</li>
                <li>Inactivo: se inhabilita su empleo en el sistema.</li>
            </ul>
        </li>
        <li>¿Registrar número de comprobante?: Indica si es obligatorio el nro de comprobante al recibir ese medio de
            pago.
        </li>
        <li>Tipo de pago: 
            <ul>
                <li>Directo: indica que se recibe el dinero directamente. Ejemplo Contado.</li>
                <li>Indirecto: indica que existe un documento respaldatorio de la operación. Ejemplo cheque.</li>
                <li>Cuenta: indica que el medio de pago ingresa a una cuenta bancaria de la empresa.</li>
            </ul>
        </li>
    </ul>
    <p></p>
    <h4>Clases Facturación E.</h4>
    <p>En este módulo se le indica al sistema en que ruta del directorio de archivos debe buscar las clases de
        facturación electrónica que hacen la comunicación con AFIP. <strong>Estos campos deberían ser modificados
            solamente por los desarrolladores del sistema.</strong></p>
    <h3>Resumen de comprobantes</h3>
    <p>Este módulo permite obtener información estadística de los movimientos de la empresa. A partir de la aplicación
        de filtros de búsqueda se puede obtener el resumen general de la empresa.</p>
    <p><strong>Filtros</strong></p>
    <p>Al hacer click sobre este módulo (al ingresar a la funcionalidad se encuentra colapsado), se despliegan una serie
        de filtros de búsqueda que permitirán obtener los datos que el usuario necesite.</p>
    <ul>
        <li>Tipos de comprobante: definir los comprobantes a buscar.</li>
        <li>Estados: estado de los comprobantes.</li>
        <li>Empresa: empresa de facturación.</li>
        <li>Desde Fecha y Hasta fecha: definen el rango de fechas del que se desea obtener la información.</li>
        <li>Desde Monto y Hasta monto: permiten filtrar comprobantes en un rango determinado.</li>
        <li>Moneda: moneda en la que se emitió el comprobante.</li>
        <li>Período
            <ul>
                <li>Resumen diario: la información de la búsqueda es presentada de forma diaria.</li>
                <li>Resumen mensual: la información es presentada en un resumen mensual.</li>
                <li>Resumen anual: la información es presentada agrupada anualmente.</li>
            </ul>
        </li>
        <li>Gráfico: permite la generación de 3 tipos diferentes de gráficos, lineal, barras o circular.</li>
    </ul>
    <h3>Mis comprobantes</h3>
    <p>Esta funcionalidad permite obtener información estadística de los comprobantes emitidos, observando solamente los
        comprobants emitidos por el usuario con el que se ha iniciado sesión.</p>
    <p>Si el usuario es un administrador del sitio, además podrá consultar los comprobantes emitidos por otros
        usuarios.</p>
    <h3>Todos los comprobantes</h3>
    <p>Acá se podrá gestionar todos los comprobantes ya emitidos en el sistema.</p>
    <p><strong>Filtros de búsqueda</strong></p>
    <p>Al hacer click sobre este módulo (al ingresar a la funcionalidad se encuentra colapsado), se despliegan una serie
        de filtros de búsqueda que permitirán obtener los datos que el usuario necesite.</p>
    <ul>
        <li>Tipos de comprobante: definir los comprobantes a buscar.</li>
        <li>Estados: estado de los comprobantes.</li>
        <li>Medios de pago utilizados en el comprobante.</li>
        <li>Empresa: empresa de facturación.</li>
        <li>Desde Fecha y Hasta fecha: definen el rango de fechas del que se desea obtener la información.</li>
        <li>Desde Monto y Hasta monto: permiten filtrar comprobantes en un rango determinado.</li>
        <li>Moneda: moneda en la que se emitió el comprobante.</li>
        <li>Cliente: a quién se emitió el comprobante.</li>
        <li>Vencidos: permite ocultar aquellos comprobantes vencidos.</li>
    </ul>
    <p><strong>Acciones permitidas</strong></p>
    <p>De acuerdo al estado del comprobante se habilitan diferentes acciones posibles. </p>
    <p>Si el comprobante está en estado borrador:</p>
    <ul>
        <li>Ojo: permite visualizar el contenido del comprobante.</li>
        <li>Lápiz: permite actualizar la información del comprobante.</li>
        <li>Cesto de basura: permite eliminar el comprobante.</li>
    </ul>
    <p>Si el comprobante está en estado completo (la factura no se informó a AFIP):</p>
    <ul>
        <li>Ojo: permite visualizar el contenido del comprobante.</li>
        <li>Lápiz: permite actualizar la información del comprobante.</li>
        <li>Flecha reintentar: permite volver a abrir el comprobante para volver a repetir el proceso de informe a AFIP
            del comprobante.
        </li>
        <li>Cesto de basura: permite eliminar el comprobante.</li>
    </ul>
    <p>Si el comprobante se encuentra cerrado (ya se informó a AFIP):</p>
    <ul>
        <li>Ojo: permite visualizar la información del comprobante.</li>
        <li>Impresora: permite imprimir el comprobante con el layout de impresión de ese comprobante.</li>
        <li>Sobre: permite enviar por e-mail el comprobante al cliente. Para que esta opción esté habilitada, el cliente
            debe contar con un e-mail válido cargado.
        </li>
    </ul>
    <h3>Lista de Factura A</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Facturas A
        emitidas durante el último mes.</p>
    <h3>Lista de Factura B</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Facturas B
        emitidas durante el último mes.</p>
    <h3>Lista de Factura X</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Facturas X
        emitidas durante el último mes.</p>
    <h3>Lista de Nota de Crédito A</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Notas de Crédito
        A emitidas durante el último mes.</p>
    <h3>Lista de Nota de Crédito B</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Notas de Crédito
        B emitidas durante el último mes.</p>
    <h3>Lista de Nota de Crédito X</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Notas de Crédito
        X emitidas durante el último mes.</p>
    <h3>Lista de Nota de Débito A</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Notas de Débito A
        emitidas durante el último mes.</p>
    <h3>Lista de Nota de Débito B</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Notas de Débito B
        emitidas durante el último mes.</p>
    <h3>Lista de Nota de Débito X</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren las Notas de Débito X
        emitidas durante el último mes.</p>
    <h3>Todas las facturas</h3>
    <p>Esta sección incluye los filtros de búsquedas predefinidos para que al ingresar se muestren directamente todas
        las facturas emitidas durante el último mes.</p>
    <h3>IVA Ventas</h3>
    <p>Permite generar el libro IVA Ventas de la empresa a partir de las ventas de la empresa.</p>
    <p><strong>Generar nuevo libro</strong></p>
    <p>Para generar un nuevo libro,</p>
    <ul>
        <li>Seleccionar la empresa a la cual se le va a generar el libro.</li>
        <li>Período de facturación que se va a seleccionar.</li>
        <li>Número de libro: este número se define en forma manual por el usuario que emitirá el libro. </li>
        <li>Alta: al hacer click en este botón el sistema automáticamente elabora el libro teniendo en cuenta los
            comprobantes de la empresa seleccionada en el período seleccionado.
        </li>
    </ul>
    <p><strong>Iconografía</strong></p>
    <ul>
        <li>Ojo: permite visualizar la información que conforma el libro diario seleccionado.</li>
        <li>Impresora: genera el documento de impresión.</li>
        <li>Flecha: descarga la información en formato Microsoft Excel.</li>
        <li>Hoja: descarga las alícuotas en formato txt.</li>
        <li>Nube: descarga los comprobantes en formato txt.</li>
    </ul>
    <h3>IVA Compras</h3>
    <p>Permite generar el libro IVA Compras de la empresa a partir de las compras de la empresa.</p>
    <p><strong>Generar nuevo libro</strong></p>
    <p>Para generar un nuevo libro,</p>
    <ul>
        <li>Seleccionar la empresa a la cual se le va a generar el libro.</li>
        <li>Período de facturación que se va a seleccionar.</li>
        <li>Número de libro: este número se define en forma manual por el usuario que emitirá el libro. </li>
        <li>Alta: al hacer click en este botón el sistema automáticamente elabora el libro teniendo en cuenta los
            comprobantes de la empresa seleccionada en el período seleccionado.
        </li>
    </ul>
    <p><strong>Iconografía</strong></p>
    <ul>
        <li>Ojo: permite visualizar la información que conforma el libro diario seleccionado.</li>
        <li>Impresora: genera el documento de impresión.</li>
        <li>Flecha: descarga la información en formato Microsoft Excel.</li>
        <li>Hoja: descarga las alícuotas en formato txt.</li>
        <li>Nube: descarga los comprobantes en formato txt.</li>
    </ul>
    <h3>Productos para IIBB</h3>
    <p>Genera el listado por empresas de los productos que se deben tener en cuenta para la liquidación del impuesto
        Ingresos Brutos.</p>
    <p>Para simplificar el proceso de cálculo, se toma por defecto en los filtros de búsqueda el primer día y el último
        del mes en curso.</p>
    <p><strong>Filtros de búsqueda</strong></p>
    <ul>
        <li>Empresa: Define la empresa en la que se buscan los productos cargados.</li>
        <li>Desde Fecha - Hasta Fecha: definel el período de fechas con el que se va a trabajar.</li>
        <li>Tipos de comprobantes:permite elegir que comprobantes deben ser tenidos en cuenta.</li>
        <li>Productos: define los productos que conformarán el listado.</li>
    </ul>
    <p><strong>Exportar</strong></p>
    <p>Permite la exportación de los datos que se visualizan por pantalla en formato Microsoft Excel.</p>
    <h2><strong></strong>Clientes</h2>
    <p>Este módulo permite el acceso a la información tanto contable cómo de conexiones de cada cliente de la empresa.</p>
    <h3>Clientes</h3>
    <p>Al ingresar a esta sección se debe proporcionar los criterios de búsquedas deseados para acceder a la información
        de un cliente o varios clientes.</p>
    <h4>Buscar texto</h4>
    <p>Es un buscador genérico en el que es posible realizar la búsqueda con cualquier dato del cliente.</p>
    <h4>Filtros</h4>
    <ul>
        <li>Número de documento: podemos buscar el cliente deseado ingresando aquí su número de DNI.</li>
        <li>Número de cliente: es el número identificatorio de los clientes en el sistema. En algunos casos es utilizado
            para realizar los pagos en los Ecopagos habilitados.
        </li>
        <li>Nombre: nombre del cliente.</li>
        <li>Apellido: apellido del cliente.</li>
        <li>Empresa: determina a qué empresa de facturación corresponde un cliente. </li>
        <li>Categoría del cliente: categoría de facturación del client (Básico, Free, Mantenimiento, VIP).</li>
        <li>Estado del cliente: estado en el que se encuentra el cliente (Habilitado / Deshabilitado). El estado del
            cliente es independiente del estado de cada una de las conexiones del mismo. 
        </li>
        <li>Zona / Barrio: lugar donde se encuentra la conexión.</li>
        <li>Nodo: nodo que provee el servicio de internet.</li>
        <li>Estado de conexión:
            <ul>
                <li style="list-style-type: none;">
                    <ul>
                        <li>Habilitada: estado que se utiliza para describir que la conexión es correcta.</li>
                        <li>Deshabilitada: conexión que ha sido finalizada.</li>
                        <li>Forzada: el cliente no ha pagado y se le ha habilitado la conexión forzadamente por un
                            tiempo determinado.
                        </li>
                        <li>Moroso: el cliente presenta deuda.</li>
                        <li>Cortado: se ha cortado el servicio por falta de pago.</li>
                        <li>De Baja: el servicio se encuentra en proceso de baja. </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>Rubro del cliente
            <ul>
                <li>Familia: la conexión ha sido realizada en un domicilio particular.</li>
                <li>Empresa: la conexión ha sido realizada en una empresa.</li>
            </ul>
        </li>
        <li>Plan: plan de internet que tiene asignado el / los clientes. En este caso se muestran todos los planes
            activos.
        </li>
        <li>Estado de contrato: permite buscar el / los contratos en un estado en particular.</li>
    </ul>
    <h4>Cuenta de un cliente</h4>
    <p>Al realizar una búsqueda de un cliente, podemos observar que uno de los botones es "$ Cuenta". Al hacer click
        sobre el mismo accederemos al resumen de movimientos contables del cliente desde que contrató el servicio.</p>
    <p>Al ingresar a la cuenta del cliente obtendremos en la parte superior de la página un resumen contable de los
        créditos, pagos y su saldo al día de la fecha.</p>
    <h5><strong>Detalle de movimientos</strong></h5>
    <p>Se detallan los movimientos contables que determinan el resumen anteriormente descripto en orden cronológico.</p>
    <h5><strong>Alta de pago</strong></h5>
    <p>Desde ese botón es posible generar un pago manual para un cliente, que por ejemplo se acerca a la oficina a
        pagar. </p>
    <p>Se debe detallar,</p>
    <ul>
        <li>Empresa: empresa a la que se le imputa el pago.</li>
        <li>Modelo de distribución societaria: si el pago se computa de manera normal o lo recibe un socio en
            particular.
        </li>
        <li>Fecha: fecha en la que se recibe el pago.</li>
        <li>Importe: monto total que va a pagar el cliente.</li>
        <li>Concepto: descripción textual de qué está abonando el cliente.</li>
    </ul>
    <p>Una vez cargados estos valores, presionar "Siguiente".</p>
    <p>En la siguiente pantalla se deben cargar todos los medios de pagos que conforman el importe total del pago. Al
        finalizar el proceso se chequea que el importe coincida con la suma de los importes de cada pago realizado.
         </p>
    <p>Por cada pago se debe informar:</p>
    <ul>
        <li>Medio de pago: cómo está realizando el pago (efectivo, cheque, etc.).</li>
        <li>Banco: si el pago ingresa por un medio de pago bancarizado, se debe detallar a qué banco corresponde la
            operación.
        </li>
        <li>Cuenta monetaria: detallar a qué cuenta bancaria ingresa el dinero.</li>
        <li>Descripción: si se desea se puede agregar un detalle manual del pago.</li>
        <li>Importe: monto del pago.</li>
    </ul>
    <p>Cuando se valida que el monto de los pagos coincide con el importe cargado inicialmente se habilita el botón
        Aceptar, el cual debe presionarse para finalizar la carga del pago.</p>
    <h5><strong>Información del cliente</strong></h5>
    <p>Al hacer click sobre el "Ojo" ingresamos a la información de las conexiones del cliente. </p>
    <p>Debajo del nombre y número de cliente encontramos una serie de funcionalidades habilitadas.</p>
    <ul>
        <li>Actualizar: permite editar los datos personales del cliente.</li>
        <li>$ Cuenta corriente: accedemos a la cuenta del cliente.</li>
        <li>Historial de Cliente: se muestra todo el detalle de los cambios en la cuenta del cliente.</li>
        <li>Tickets: accede a todos los tickets del cliente</li>
        <li>Credencial del cliente: Permite emitir la credencial de pago del cliente.</li>
        <li>Cambiar Empresa: permite cambiar la empresa de facturación del cliente.</li>
        <li>Descuentos: Se detallan los descuentos que tiene asignado el cliente y por cuánto tiempo.</li>
        <li>Productos a facturar: desde aquí se le indica si es necesario facturarle un producto en particular. Este
            producto será facturado en la próxima factura que se le emita al cliente.
        </li>
        <li>Plan de Pago: permite la generación de un plan de pago. Importante: al generarse un plan de pago, la deuda
            se reduce a 0.
        </li>
        <li>Comprobantes: se puede acceder a los comprobantes del cliente.</li>
    </ul>
    <p><strong>Contratos</strong></p>
    <p>Si el cliente tiene un contrato activo se visualizará en este apartado. Caso contrario podemos proceder a generar
        uno nuevo desde "Nuevo Contrato".</p>
    <p>Para generar un nuevo contrato se debe definir,</p>
    <ul>
        <li>Vendedor del servicio.</li>
        <li>Plan que se le asignará.</li>
        <li>Cargos de instalación.</li>
        <li>Plan de financiación de la instalación.</li>
        <li>Detallar si se aplican descuentos en la instalación.</li>
        <li>Horario de instalación preferido.</li>
        <li>SI la conexión se realizará en el domicilio del cliente, se puede evitar volver a cargar la información
            seleccionando la opción "Misma dirección que el cliente". Caso contrario se debera geolocalizar la nueva
            dirección de instalación.
        </li>
    </ul>
    <h3>Deudores</h3>
    <p>Este listado permite acceder rápidamente al detalle de los clientes que presentan facturas adeudadas. </p>
    <p>Es posible determinar en los filtros de búsqueda la cantidad de facturas adeudadas por los clientes.</p>
    <h3>Clientes con saldo a favor</h3>
    <p>Este listado permite conocer cuánto dinero ha ingresado a la empresa a partir de pagos adelantados de los
        clientes.</p>
    <h3>Instalaciones pendientes</h3>
    <p></p>
    <p><span style="color: inherit; font-family: inherit; font-size: 24.5px; font-weight: bold;">Principal</span></p>
    <p>Para ingresar a la pantalla principal de clientes, ingrese a través del menu a "Clientes". Aquí encontrará una
        tabla con una lista de todos los clientes cargados. Sólo se muestran los principales datos de cada cliente.</p>
    <h4>Buscador</h4>
    <p>En la parte superior de la pantalla encontrará un buscador que le permitirá buscar un cliente determinado en la
        base de datos de clientes. Para esto deberá introducir algún texto de búsqueda y luego presionar la tecla
        "Enter". La lista de clientes se actualizará y mostrará unicamente los clientes que coinciden con el criterio de
        búsqueda introducido.</p>
    <p>Para volver a mostrar todos los clientes, puede presionar la tecla "Esc" o puede pulsar el botón "x" que se
        encuentra a la derecha del buscador.</p>
    <h4>Filtros y ordenamiento</h4>
    <p>En la parte superior de la tabla de clientes, usted encontrará una serie de filtros que pueden ser aplicados
        sobre la lista de clientes para realizar búsquedas acotadas. Puede utilizar un único filtro a la vez o combinar
        varios de ellos. Para volver a cargar los todos los clientes simplemente presione la tecla "Esc".</p>
    <p>Si observa los nombres de las columnas de la tabla, notará que algunos de estos nombres tienen un color celeste.
        Si usted hace clic sobre alguno de ellos, la lista de clientes se ordenará de forma creciente para los valores
        de esa columna. En caso de ser una columna que contiene texto, el texto se ordenará alfabéticamente. Si vuelve a
        presionar sobre la columna, los clientes se ordenarán en forma decreciente.</p>
    <h3>Perfiles adicionales</h3>
    <p>En caso de que usted necesite registrar algún dato de sus clientes que no se encuentre dentro de los datos por
        defecto provistos por el Sistema, usted podrá generar un nuevo tipo de dato. Para esto deberá crear un nuevo
        Perfil. Los perfiles de cliente son datos que le ayudarán a segmentar aún más la base de datos de cliente, y que
        podrá utilizar en cualquier momento para fortalecer su empresa.</p>
    <h1 id="stock">Stock</h1>
    <p>Usted podrá controlar de manera rápida y sencilla el stock de cada uno de sus productos. El manejo de stock
        contempla dos tipos de operaciones:</p>
    <ul>
        <li><span class="green glyphicon glyphicon-arrow-up"></span> Ingreso de stock: Esta operación implica un aumento
            de stock para un producto.
        </li>
        <li><span class="red glyphicon glyphicon-arrow-down"></span> Egreso de stock: Esta operación implica una
            reducción de stock para un producto.
        </li>
    </ul>
    <h4>Movimientos de stock manuales</h4>
    <p>Para efectuar un movimiento de stock de forma manual, primero deberá buscar el producto en la lista de productos.
        Luego, desde la lista de operaciones, deberá seleccionar "Movimiento entrante" o "Movimiento saliente". Se
        abrirá una nueva pantalla donde deberá ingresar la cantidad de productos correspondientes al movimiento, y una
        descripción o concepto que explique el motivo del movimiento. Luego deberá presionar "Crear", con lo que el
        movimiento quedará registrado, y el stock del producto será actualizado.</p>
    <h4>Venta y Movimientos de stock</h4>
    <p>Al vender, usted debe especificar la cantidad de productos de la venta. Una vez que cierre la factura, se
        generará automáticamente un movimiento de stock por cada uno de los productos de la venta. Recuerde que la venta
        deberá ser cerrada presionando el botón "Cerrar". En caso de que sólo guarde los datos de la venta (por ejemplo
        a modo de presupuesto), no se efectuará ningún movimiento de stock.</p>
    <h2><strong style="color: inherit; font-family: inherit; font-size: 24.5px;">Tipos de Documento</strong></h2>
    <p>Todos los tipos de documentos que son válidos para operar en el sistema se gestionan desde este módulo.</p>
    <h3>Condiciones frente a IVA</h3>
    <p>Esta funcionalidad contempla los comprobantes que se deben emitir a un cliente teniendo en cuenta su condición
        frente a IVA. Por ejemplo si el cliente es un Consumidor Final debe recibir Factura B pero no A.</p>
    <p><strong>Descripción de campos</strong></p>
    <ul>
        <li>Nombre: nombre de la condición frente a IVA (Consumidor Final, Responsable Inscripto, etc.)</li>
        <li>Tipos de comprobantes: tipos de comprobantes que un cliente con esa condición puede recibir.</li>
        <li>Tipos de documentos: tipos de documentos válidos para ese documento (DNI, CUIT, etc.)</li>
        <li>Exento: indicar si la condición es exento de IVA.</li>
    </ul>
    <h3>Categorías de clientes</h3>
    <p>Desde este módulo se permite la gestión de categorías de clientes. Se pueden modificar las categorías existentes
        y generar nuevas categorías según se lo necesite.</p>
    <h4>Generar Nueva Categoría</h4>
    <ul>
        <li>Click en "Alta de categoría de cliente".</li>
        <li>Completar los campos del formulario.
            <ul>
                <li>Nombre: nombre de la categoría.</li>
                <li>Identificador Sequre: código que vincula la categoría con el servicio ofrecido por Sequre.</li>
                <li>Facturar: si se habilita esta opción se tiene en cuenta esta categoría para la facturación por
                    lotes.
                </li>
                <li>Días de aviso para corte: este campo define cuando se debe notificar del corte a los clientes.</li>
                <li>Color: color que identifica la categoría.</li>
                <li>Porcentaje a facturar: define que porcentaje del total del plan se le factura a los clientes.</li>
                <li>Tolerancia de deuda: define el monto que se tolera que el cliente deba antes de proceder al corte
                    del servicio.
                </li>
                <li>Servicio disponible: si se brinda servicio de internet, se debe habilitar esta opción.</li>
                <li>Estado: define si la categoría está habilitada para ser utilizada en el sistema.</li>
            </ul>
        </li>
        <li>Click en el botón "Alta".</li>
    </ul>
    <h3>Rubros de Cliente</h3>
    <p>En este módulo se generan los rubros de los clientes.</p>
    <h4>Generar un nuevo rubro</h4>
    <ul>
        <li>Click en generar un nuevo rubro.</li>
        <li>Completar los campos solicitados.
            <ul>
                <li>Padre: si es un subrubro, especificar el rubo padre.</li>
                <li>Nombre</li>
                <li>Estado: Habilitado / Deshabilitado.</li>
            </ul>
        </li>
    </ul>
    <h3>Zonas</h3>
    <p>En este módulo se cargan todas las zonas y barrios del sistema. </p>
    <h4>Alta de zona</h4>
    <ul>
        <li>Click en "Alta Zona / Barrio".</li>
        <li>Completar los datos.
            <ul>
                <li>Nombre </li>
                <li>Tipo: País / Provincia /  Departamento / Localidad / Zona ó Barrio.</li>
                <li>Estado</li>
                <li>Código Postal</li>
            </ul>
        </li>
        <li>Click en "Alta"</li>
    </ul>
    <h3>Descuentos</h3>
    <p>Desde esta funcionalidad se accede al listado de tipos de descuentos que se pueden brindar a un cliente.</p>
    <h4>Alta de Descuento</h4>
    <p>Para generar un nuevo tipo de descuento:</p>
    <ul>
        <li>Click en Alta de Descuento.</li>
        <li>Completar los datos
            <ul>
                <li>Nombre</li>
                <li>Estado</li>
                <li>Tipo: porcentaje o monto fijo.</li>
                <li>Valor: este campo depende del anterior. De esta manera podemos formar 10% de descuento o $200, según
                    los parámetros elegidos.
                </li>
                <li>Fecha de inicio de Vigencia y Fecha de Fin: Define por cuánto tiempo se aplica este descuento.</li>
                <li>Períodos: define por cuántos períodos de facturación se aplica el descuento.</li>
                <li>Aplica a: el descuento se puede aplicar a un cliente o a un producto.</li>
                <li>Valor: se debe indicar si se aplica sobre el total de facturación o sobre el valor de un producto.
                </li>
            </ul>
        </li>
        <li>Click en "Alta" para finalizar el proceso.</li>
    </ul>
    <h3>Facturado y Cobrado</h3>
    <p>Permite tener un resumen real que contrasta lo que ha sido facturado y el total efectivo que ha sido cobrado.</p>
    <h2>Productos</h2>
    <p>Este módulo permite la gestión de los productos y servicios ofrecidos por la empresa.</p>
    <h4>Productos</h4>
    <p><span style="color: inherit; font-family: inherit; font-weight: bold;">Stock</span></p>
    <p>Usted podrá controlar de manera rápida y sencilla el stock de cada uno de sus productos. El manejo de stock
        contempla dos tipos de operaciones:</p>
    <ul>
        <li><span class="green glyphicon glyphicon-arrow-up"></span> Ingreso de stock: Esta operación implica un aumento
            de stock para un producto.
        </li>
        <li><span class="red glyphicon glyphicon-arrow-down"></span> Egreso de stock: Esta operación implica una
            reducción de stock para un producto.
        </li>
    </ul>
    <h5>Movimientos de stock manuales</h5>
    <p>Para efectuar un movimiento de stock de forma manual, primero deberá buscar el producto en la lista de productos.
        Luego, desde la lista de operaciones, deberá seleccionar "Movimiento entrante" o "Movimiento saliente". Se
        abrirá una nueva pantalla donde deberá ingresar la cantidad de productos correspondientes al movimiento, y una
        descripción o concepto que explique el motivo del movimiento. Luego deberá presionar "Crear", con lo que el
        movimiento quedará registrado, y el stock del producto será actualizado.</p>
    <h5>Venta y Movimientos de stock</h5>
    <p>Al vender, usted debe especificar la cantidad de productos de la venta. Una vez que cierre la factura, se
        generará automáticamente un movimiento de stock por cada uno de los productos de la venta. Recuerde que la venta
        deberá ser cerrada presionando el botón "Cerrar". En caso de que sólo guarde los datos de la venta (por ejemplo
        a modo de presupuesto), no se efectuará ningún movimiento de stock.</p>
    <p><strong>Tipos de comisión</strong></p>
    <p>Se define los tipos de comisión que se aplican para la venta de productos.</p>

    <h4>Buscador</h4>
    <p>
        En la parte superior de la pantalla encontrará un buscador que le permitirá buscar
        un producto determinado en la base de datos de producto. Para esto podrá utilizar
        un lector de códigos de barra, o podrá introducir algún texto de búsqueda.
        Para buscar simplemente escriba el texto a buscar y presione la tecla "Enter".
        La lista de productos se actualizará y mostrará unicamente los productos que
        coinciden con el criterio de búsqueda introducido.
    </p>
    <p>
        Para volver a mostrar todos los productos, puede presionar la tecla "Esc" o puede
        pulsar el botón "x" que se encuentra a la derecha del buscador.
    </p>
    <?php if (Yii::$app->params['dropdown-operations-list']): ?>
        <p>
            Cada item en la lista cuenta con una serie de botones para realizar diversas
            operaciones sobre cada producto:
        </p>
        <ul style="line-height: 25px;">
            <li><span class="glyphicon glyphicon-tags"></span>&nbsp; Muestra historial de precios del producto.</li>
            <li><span class="glyphicon glyphicon-stats"></span>&nbsp; Muestra historial de stock del producto.</li>
        </ul>
        <p>
            En la última columna encontrará un botón que desplegará un menú al hacer clic sobre él. Este menú
            cuenta con una serie de operaciones que pueden ser realizadas para cada producto:
        </p>
        <ul>
            <li><span class="glyphicon glyphicon-eye-open"></span>&nbsp; Ver: Muestra todos los datos del producto en
                detalle.
            </li>
            <li><span class="glyphicon glyphicon-pencil"></span>&nbsp; Actualizar: Permite actualizar los datos del
                producto.
            </li>
            <li><span class="glyphicon glyphicon-arrow-up"></span>&nbsp; Movimiento entrante: Permite generar un
                movimiento de stock entrante manual para el producto.
            </li>
            <li><span class="glyphicon glyphicon-arrow-down"></span>&nbsp; Movimiento saliente: Permite generar un
                movimiento de stock saliente manual para el producto.
            </li>
            <li><span class="glyphicon glyphicon-barcode"></span>&nbsp; Imprimir códigos de barra: Permite imprimir una
                plancha de códigos de barra.
            </li>
            <li><span class="glyphicon glyphicon-trash"></span>&nbsp; Eliminar: Permite eliminar un producto.</li>
        </ul>
        <p>
            Para acceder a cada una de las operaciones, simplemente haga clic en el botón correspondiente.
        </p>
    <?php else: ?>
        <p>
            Cada item en la lista cuenta con una serie de botones para realizar diversas
            operaciones sobre cada producto:
        </p>
        <ul style="line-height: 25px;">
            <li><span class="glyphicon glyphicon-tags"></span>&nbsp; Muestra historial de precios del producto.</li>
            <li><span class="glyphicon glyphicon-stats"></span>&nbsp; Muestra historial de stock del producto.</li>
            <li><span class="glyphicon glyphicon-eye-open"></span>&nbsp; Muestra todos los datos del producto en
                detalle.
            </li>
            <li><span class="glyphicon glyphicon-pencil"></span>&nbsp; Permite actualizar los datos del producto.</li>
            <li><span class="glyphicon glyphicon-trash"></span>&nbsp; Permite eliminar un producto.</li>
            <li><span class="glyphicon glyphicon-barcode"></span>&nbsp; Permite imprimir una plancha de códigos de
                barra.
            </li>
        </ul>
        <p>
            Para acceder a cada una de las operaciones, simplemente haga clic en el botón correspondiente.
        </p>
    <?php endif; ?>
    <h4>Filtros y ordenamiento</h4>
    <p>
        En la parte superior de la tabla de productos, usted encontrará una serie de
        filtros que pueden ser aplicados sobre la lista de productos para realizar
        búsquedas acotadas. Puede utilizar un único filtro a la vez o combinar varios
        de ellos. Para volver a cargar los todos los productos simplemente presione la
        tecla "Esc".
    </p>
    <p>
        Si observa los nombres de las columnas de la tabla, notará que algunos de estos nombres
        tienen un color celeste. Si usted hace clic sobre alguno de ellos, la lista de productos
        se ordenará de forma creciente para los valores de esa columna. En caso de ser una columna
        que contiene texto, el texto se ordenará alfabéticamente. Si vuelve a presionar sobre la
        columna, los productos se ordenarán en forma decreciente.
    </p>

    <h3><?= Yii::t('help', 'Prices'); ?></h3>
    <p>
        Los precios de los productos contemplan IVA, precio bruto y precio final, además de una fecha
        de vencimiento optativa que se utiliza como recordatorio para renovar el precio de un producto luego
        de cierto tiempo.
    </p>
    <p>
        Cada producto almacena un historial de precios, el cual puede ser accedido en cualquier momento
        para evaluar su evolución a través del tiempo.
    </p>

    <h4>Actualización de precios manual</h4>
    <p>
        Para ingresar a la sección de actualización de precios, ingrese a "Productos" > "Actualización
        de precios".
    </p>
    <p>
        Aquí usted encontrará una tabla similar a la que se encuentra en la sección principal de Productos,
        pero con una serie de herramientas que le permitirán actualizar los precios de los productos de
        forma rápida y sencilla. Podrá utilizar las mismas funciones de filtrado y búsqueda.
    </p>
    <p>
        La columna "Precio bruto" muestra el precio del producto sin IVA, mientras que la columna
        "Precio final" muestra el precio final del producto, incluyendo el IVA. Usted podrá actualizar
        el precio de un producto cargando el precio bruto, con lo que el sistema automáticamente
        actualizará el precio final, o cargando el precio final, con lo que el precio bruto
        será calculado automáticamente.
    </p>
    <p>
        Para actualizar un precio, simplemente ingrese el nuevo precio en el campo correspondiente
        y presione la tecla "Enter".
    </p>

    <h4>Actualización de precios por lotes</h4>
    <p>
        Para ingresar a la sección de actualización de precios, ingrese a "Productos" > "Actualización
        de precios". Las herramientas para la actualización de precios por lotes se encuentran al final
        de la página. Para abrir estas herramientas, presione la barra "Actualizador de precios".
    </p>
    <p>
        La actualización de precios por lotes permite introducir un valor porcentual que se aplicará
        a todos los productos deseados. Este valor puede ser tanto positivo, para aumentar los precios,
        como negativo, para reducirlos. Existen dos opciones a la hora de utilizar la actualización de precios por
        lotes:
    </p>
    <ul>
        <li>
            Actualizar los precios de todos los productos: Bajo esta modalidad, los precios de todos los productos
            habilitados
            serán actualizados.
        </li>
        <li>
            Actualizar los precios de los productos seleccionados: Sólo los precios seleccionados de la tabla
            serán actualizados. Para seleccionar un producto, haga clic en la casilla <input type="checkbox">.
        </li>
        <li>
            Actualizar los precios de los productos por categoría: Deberá seleccionar una categoría. Todos los precios
            de los productos
            pertenecientes a dicha categoría, serán actualizados.
        </li>
    </ul>
    <p></p>

    <h1 id="customer">Notificaciones</h1>
    <p>
        El módulo de notificaciones permite crear, configurar y programar notificaciones a los clientes. También
        es posible enviar notificaciones manualmente.
    </p>
    <h4>Alta</h4>
    <p>
        Para dar de alta a una nueva notificación, debe ingresar a ISP > Notificaciones en la barra de navegación
        superior. Luego, presione el botón verde "Alta Notificación". A continuación será guiado por una serie de
        pasos para completar el alta.
    </p>
    <h4>Transport</h4>
    <p>
        Transport hace referencia al medio por el cual se efectuará el envío de la notificación. Este puede ser Email,
        SMS o Portal Captivo. Cada transport posee configuraciones particulares.
    </p>
    <h4>Destinatarios</h4>
    <p>
        Para definir un grupo de destinatarios, es posible aplicar filtros o seleccionar a los clientes manualmente.
        Al aplicar filtros, todos los clientes que cumplan con los filtros aplicados, y cuenten con la información de
        contacto requerida para cada tipo particular de transport, recibirán la notificación.
    </p>
    <p>
        Puede crear más de un conjunto de Destinatarios por notificación; la notificación será enviada sólo una vez
        a cada cliente, aunque el cliente cumpla con los filtros de más de un grupo.
    </p>
    <h2>Pagos</h2>
    <p>Este módulo centraliza la información de pagos de la empresa.</p>
    <h3>Medios de pago</h3>
    <p>Esta funcionalidad permite la gestión integral de los medios de pagos habilitados en el sistema.</p>
    <p><strong>Descripción de campos</strong></p>
    <ul>
        <li>Nombre: nombre del medio de pago.</li>
        <li>Estado:
            <ul>
                <li>Disponible: se puede utilizar en el sistema</li>
                <li>Inactivo: se inhabilita su empleo en el sistema.</li>
            </ul>
        </li>
        <li>¿Registrar número de comprobante?: Indica si es obligatorio el nro de comprobante al recibir ese medio de
            pago.
        </li>
        <li>Tipo de pago: 
            <ul>
                <li>Directo: indica que se recibe el dinero directamente. Ejemplo Contado.</li>
                <li>Indirecto: indica que existe un documento respaldatorio de la operación. Ejemplo cheque.</li>
                <li>Cuenta: indica que el medio de pago ingresa a una cuenta bancaria de la empresa.</li>
            </ul>
        </li>
    </ul>
    <p></p>
    <p><span style="color: inherit; font-family: inherit; font-size: 24.5px; font-weight: bold;">Planes de pago</span>
    </p>
    <p>Listado de planes de pagos generados. Es posible aplicar los filtros de búsqueda para buscar los resultados de
        interés.</p>
    <p><span style="color: inherit; font-family: inherit; font-size: 24.5px; font-weight: bold;"> </span></p>
    <h3>Archivos de Pago Fácil</h3>
    <p>Permite visualizar los comprobantes de pagos que han sido importados desde Pago Fácil y también generar la
        importación de un nuevo archivo.</p>
    <h4>Importar Archivo de Pago Fácil</h4>
    <ul>
        <li>Click en Importar Archivo.</li>
        <li>Seleccionar Archivo desde la PC que se desea importar.</li>
        <li>Seleccionar el Banco al que ingresó el dinero.</li>
        <li>Seleccionar la cuenta monetaria del banco.</li>
        <li>Click en Importar.</li>
    </ul>
    <p></p>
    <p></p>
    <h2>Proveedores</h2>
    <h3>Proveedores</h3>
    <p>Listado de proveedores de la empresa.</p>
    <ul>
        <li>Comprobantes: Permite conocer los comprobantes emitidos a ese proveedor.</li>
        <li>Pagos: permite conocer los pagos realizados a ese proveedor.</li>
        <li>Cuenta: accede a la cuenta corriente del proveedor.</li>
    </ul>
    <p></p>
    <p>
        <span style="color: inherit; font-family: inherit; font-size: 24.5px; font-weight: bold;">Deuda a proveedores</span>
    </p>
    <p>Listado con el detalle de deuda a proveedores.</p>
    <h3>Comprobantes de proveedor</h3>
    <p>Listado de comprobantes emitidos por los proveedores de la empresa.</p>
    <h3>Pagos a proveedor</h3>
    <p>Listado de pagos a proveedores.</p>
    <p></p>
    <h3>Facturas y pagos de Proveedores</h3>
    <p></p>

    <h2><span>Contabilidad</span></h2>
    <h3>Tipos de Entidad Monetaria</h3>
    <div> Listado con los tipos de entidades monetarias. Por ejemplo bancos, cajas u otros.<br/>
        <div></div>
        <h3>Entidades Monetarias</h3>
        <div> Listado de bancos, cajas u otros que estén clasificados dentro de un tipo de entidad monetaria.</div>
        <div></div>
        <h3>Cuenta Monetaria</h3>
        <div> Cuenta específica donde se recauda/guarda dinero. Cada una de estas cuentas está asociada a una entidad
            monetaria, tiene una moneda, empresa y registra los movimientos monetarios.
        </div>
        <div></div>
        <h3>Tipos de Operaciones</h3>
        <div> Listado de operaciones que se puede realizar con una entidad monetaria.</div>
        <div></div>
        <h3>Resumen bancarios</h3>
        <div> Permite la carga de los resúmenes bancarios para su posterior conciliación.</div>
        <div></div>
        <h3>Conciliaciones</h3>
        <div> Permite realizar conciliaciones entre los movimientos contables realizados y los resúmenes bancarios.
        </div>
        <div></div>
        <h3>Plan de Cuentas</h3>
        <div> Listado de cuentas contables.</div>
        <div></div>
        <h3>Configuración de cuentas</h3>
        <div>  Permite la configuración de los asientos automáticos, permitiendo configurar las cuentas contables
            asociadas a cada evento contable (Factura de compra, venta, pago, cobro, etc).
        </div>
        <div></div>
        <h3>Asiento Manual</h3>
        <div> Permite realizar asientos contables de forma manual, dando la posibilidad de seleccionar las cuentas
            deudoras y acreedoras.
        </div>
        <div></div>
        <h3>Libro diario</h3>
        <div> Listado de movimientos contables con formato de libro diario.</div>
        <div></div>
        <h3>Libro Maestro</h3>
        <div>  Libro maestro contable, tiene los importes totales de cada cuenta contable.</div>
        <div></div>
        <h3>Períodos Contables</h3>
        <div>  Permite la carga de los peoriodos contables a los cuales se les generan los asientos contables.</div>
        <div></div>
        <h3>Cheques</h3>
        <div> Administración de los cheques manejados por la empresa, permite dar de alta cheques propios y de terceros
            y modificar el estado de cada uno.
        </div>
        <div></div>
        <h3>Chequera</h3>
        <div>Listado de chequeras propias.</div>
        <div></div>
        <h3>Caja Chica</h3>
        <div>Permite la gestión de la caja chica de la empresa, permite dar de alta movimientos y cerrarla cuando sea
            necesario.
        </div>
        <div></div>
        <h2>Socios</h2>
        <h3> Socio</h3>
        <div>   Listado de socios con la posibilidad de ver el estado de cuenta de cada uno y realizar acciones de
            ingreso y egreso de dinero.
        </div>
        <div></div>
        <h3>Modelo de Distribución Societaria</h3>
        <div>  Permite realizar la distribución de los porcentajes de participación societaria para cada empresa.</div>
        <div></div>
        <h3>Liquidación</h3>
        <div>  Permite hacer una liquidación de ingresos y egresos en base al Modelo de Distribución societaria, no
            genera asientos contables.
        </div>
        <div></div>
        <h3>Liquidaciones</h3>
        <div>  Listado de las liquidaciones realizadas a cada socio. Permite la visualización de los movimientos que se
            tuvo en cuenta para cada liquidación.
        </div>
        <div></div>
        <h2>Aplicación</h2>
        <h3>Logs</h3>
        <p>Es el listado de logs del sistema. Aquí es posible buscar qué usuario realizó una acción específica.</p>
        <p>Para obtener esta información se puede aplicar los filtros de búsqueda para encontrar lo que estamos
            buscando.</p>
        <h3>Empresas</h3>
        <p>Funcionalidad que permite generar las empresas de facturación del sistema.</p>
        <p>Al dar de alta una empresa es necesario definir,</p>
        <ul>
            <li style="list-style-type: none;">
                <ul>
                    <li>Datos de la empresa (Nombre, Dirección, Condición frente a IVA, CUIT, Inicio de Actividades)
                        <ul>
                            <li>Certificado de Facturación Electrónica y Key son archivos que se obtienen desde la
                                página de AFIP. Ambos son necesarios solamente si la empresa emite factura electrónica.
                            </li>
                            <li>Por defecto: si se habilita esta opción, la empresa será la que se cargue por defecto al
                                momento de emitir una factura.
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
        <h3>Puntos de ventas</h3>
        <p>Por cada empresa generada es necesario generar los puntos de venta.</p>
        <h3>Configuración</h3>
        <h4>Contabilidad</h4>
        <p>Este apartado solamente debería ser configurado por desarrolladores del sistema.</p>
        <p></p>
        <h4></h4>
        <h4>Gestión de stock</h4>
        <ul>
            <li><span>Habilitar inventario secundario: se permite contar en los productos un stock secundario. Por ejemplo 1 caja de tornillos, 100 tornillos.</span>
            </li>
            <li><span>Stock estricto: si se habilita esta opción cuando el stock del producto llega a 0 no se puede continuar comercializando ese producto.</span>
            </li>
        </ul>
        <h4><span>Agenda</span></h4>
        <p><span>Parámetros generales para configurar el módulo de Agenda del sistema.</span></p>
        <ul>
            <li><span><span> Revisar tareas vencidas al iniciar sesión: <span>Indica si se revisarán las tareas vencidas de un usuario cuando loguee o no.</span></span></span>
            </li>
            <li><span><span><span>Timeout para revisión de tareas vencidas (s): el tiempo que debe transcurrir para considerar que una tarea se encuentra vencida.</span></span></span>
            </li>
            <li>
                <span><span><span>Hora de inicio de día laboral: cuando se considera que arranca el día de trabajo.</span></span></span>
            </li>
            <li><span><span><span>Hora de fin de día laboral: cuando se considera que finaliza el día de trabajo.</span></span></span>
            </li>
            <li><span><span><span>Cantidad de horas laborables por día.</span></span></span></li>
        </ul>
        <h4><span><span><span>Media</span></span></span></h4>
        <p>Aspectos generales que definen la subida de archivos multimedia. </p>
        <h4>Productos</h4>
        <ul>
            <li><span>¿Mostrar imágenes en la lista de productos? : Si se habilita esta opción, en el listado de productos se muestran las imágenes de los mismos.</span>
            </li>
            <li><span>Unidad por defecto: código de la unidad que por defecto maneja el sistema.</span></li>
        </ul>
        <h4><span>Clientes</span></h4>
        <p><span>Permite detallar si el domicilio es obligatorio en el alta de un cliente.</span></p>
        <h4><span>Sequre</span></h4>
        <p><span></span>Permite configurar el porcentaje de tráfico P2P</p>
        <h4>la empresa</h4>
        <p>Parámetros de configuración que determinan, por ejemplo cómo se realiza la comunicación del sistema de
            gestión con el sistema de mesa de ayuda.</p>
        <p>Estos parámetros deberían ser modificados solamente por los desarrolladores del sistema.</p>
        <h4>Ecopago</h4>
        <p>Parámetros de configuración para que el cobro en los Ecopagos sea factible. </p>
        <p><strong>Detalle de parámetros</strong></p>
        <ul>
            <li><span>Método de pago utilizado por defecto para pagos de Ecopagos</span></li>
            <li><span><span>Tipo de entidad bancaria utilizada para mostrar a que entidades bancarias rendir dinero de cierres de lote.</span></span>
            </li>
            <li><span><span><span>ID de la app para Google Chrome que se utiliza para realizar las impresiones en las ticketeras. Es necesario que este ID sea valido y sea el mismo que provee la instalacion de la app en el explorador Chrome (en vista de Extensiones este ID es visible).</span></span></span>
            </li>
            <li>
                <span><span><span><span>Limite de pago: límite de un pago de un cliente en un Ecopago.</span></span></span></span>
            </li>
            <li><span><span><span><span>Caja en la que ingresan los pagos de cada Ecopago.</span></span></span></span>
            </li>
            <li>
                <span><span><span><span><span>Empresa a la que se le asocian los movimientos de la rendicion.</span></span></span></span></span>
            </li>
            <li><span>Tipo de comprobante con el que facturan los ecopagos.</span></li>
            <li><span><span>Tipo de comprobante con el que se hacen las notas de debito de ecopagos.</span></span></li>
            <li><span>Tipo de comprobante con el que se hacen las notas de credito de ecopagos.</span></li>
        </ul>
        <h4><span>General</span></h4>
        <p><span>Parámetros generales de configuración del sistema.</span></p>
        <ul>
            <li><span><span>Numero de último ADS impreso</span></span></li>
            <li><span><span>Meses sin aplicar aumento a clientes: si se genera una nueva conexión y se produce un aumento, los clientes nuevos no deben ver reflejados automáticamente los aumentos en sus cuentas.</span></span>
            </li>
            <li><span><span><span>Host para servicio wkhtmltopdf: ubicación de la librería que convierte los archivos html a pdf</span></span></span>
            </li>
            <li><span><span><span><span>Puerto para servicio wkhtmltopdf: puerto que utiliza el servicio para conectarse.</span></span></span></span>
            </li>
        </ul>
        <h4><span><span><span><span>Socios</span></span></span></span></h4>
        <p>
            <span><span><span><span>Parámetros de configuración de las cuentas de cobro y pago.</span></span></span></span>
        </p>
        <h4><span><span><span><span>Tickets</span></span></span></span></h4>
        <p>
            <span><span><span><span>Parámetros de configuración de tickets. Definen el comportamiento del módulo tickets.</span></span></span></span>
        </p>
        <p>
            <span><span><span><span>Se recomienda que sean modificados solamente por los desarrolladores del sistema.</span></span></span></span>
        </p>
        <ul>
            <li><span><span><span><span><span>Timeout para cerrar automáticamente los tickets abiertos (en días): 10 días por defecto</span></span></span></span></span>
            </li>
            <li><span><span><span><span><span><span>Límite de elementos para cada página de Ticket: </span>Setea el límite que las páginas utilizadas en Ticket (principalmente listado de observaciones) muestran</span></span></span></span></span>
            </li>
            <li>ID del estado de un nuevo ticket.</li>
            <li>ID de categoría de la Nota de Crédito.</li>
            <li><span>Indica el ID de la categoria de factura.</span></li>
        </ul>
        <h4><span>Notificaciones por correo</span></h4>
        <p>Parámetros que son enviados en las notificaciones por correo:</p>
        <ul>
            <li>Teléfono servicio técnico.</li>
            <li>Teléfono 1 administración.</li>
            <li>Teléfono 2 administración.</li>
            <li>Ante título del mailing.</li>
        </ul>
        <h4>Vendedores</h4>
        <p> Se define la
            c<span>antidad de meses que un cliente debe pagar para que el vendedor no sea sancionado.</span></p>
        <h2>La empresa</h2>
        <h3>Servidores</h3>
        <p>Permite gestionar los servidores que brindan servicio de internet.</p>
        <h4>Mover clientes</h4>
        <p>En cada servidor es posible mover los clientes a otro, de manera de que ante algún desperfecto técnico los
            usuarios puedan continuar recibiendo el servicio.</p>
        <p>Para mover clientes a otro server es necesario definir a qué server se moverán los mismos.</p>
        <h4>Restaurar clientes</h4>
        <p>Es el proceso que revierte la asignación realizada por "Mover clientes". Permite restaurar los clientes al
            ISP original</p>
        <h3>Nodos</h3>
        <p>Por cada ISP se generan nodos.</p>
        <p>Es el proceso que revierte el Mover Clientes.</p>
        <p><span></span></p>
    </div>
    <?php } ?>
</div>