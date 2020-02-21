<?php
return [
    //Data
    'adminEmail' => '*******',
    'web_title' => '*******', //Por ejemplo, "Arya"
    'web_logo' => 'http://localhost/arya/web/images/arya.png', //full path, por ejemplo 'http://localhost/arya/web/images/quoma.png',
    
    //Behaviors:
    
    /*** Products ***/
    /** 
     * Al crear un nuevo producto, primero se debe cargar el codigo de barras 
     * (o generar uno nuevo) antes de cargar el resto de los datos.
     * Type: boolean
     */
    'code_first_on_create'=>true,
    
    /**
     * Ubicación del bin de genbarcode
     */
    'genbarcode_location'=>'/usr/local/bin/genbarcode',
    
    /**
     * TODO:
     * Codigos de barra para productos variables (peso, longitud, etc.)
     * TODO:
     * '22'=>[
            'code_length'=>4,
            'variable_length'=>6,
        ],
        '23'=>[
            'code_length'=>4,
            'variable_length'=>8,
        ],
     */
    'barcode_variable'=>[
        'prefix'=>['22','23','25'],
        'code_length'=>4,
        'variable_length'=>6,
    ],
    
    /**
     * Action por defecto del boton de historial de stock de product/index.
     * graph | index
     */
    'stock_history_action'=>'graph',
    
    /**
     * Como se muestran las operaciones en la grilla de productos. Si es true,
     * se muestran como un boton con un dropdown. Si es false, se muestran
     * solo los iconos.
     */
    'dropdown-operations-list'=>true,
    
    /**
     * Donde se muestran las categorias en el grid de product/index?
     * Valores posibles:
     * 'name': Debajo del nombre
     * 'column': En una columna aparte
     * false: No muestra las categorias
     */
    'categories-location'=>'name',
    
    /**
     * Se debe mostrar la columna con el codigo del producto en product/index?
     */
    'show-code-column'=>true,
    
    /**
     * No permitir stock negativo?
     */
    'strict_stock'=>false,
 
    /**
     * Activado Plan Product?
     */
    'plan_product'=>true,
    
    /**
     * Activado Plan de financiación?
     */
    'funding_plan'=>true,
    
    /*** End products ***/

    /*** Prices ***/    
    /**
     * Cantidad de dias a partir de los cuales un precio no actualizado se
     * muestra en color de advertencia.
     */
    'update-price-warning'=>30,
    
    
    /*** End prices ***/
    
    /*** Commons ***/
    /**
     * Inteligent placeholder: en evento focusin en el campo, el valor del mismo
     * pasa a ser el placeholder y el valor se limpia.
     */
    'inteligent_placeholder'=>[
        //En productos:
        'product'=>[
            'search_text'=>true
        ],
        'customer'=>[
            'search_text'=>true
        ]
        //...
    ],
    
    /**
     * Seleccionar texto al hacer click en un inputColumn
     */
    'auto_select_input_column'=>true,
    
    /**
     * Estilos utilizados por el layout de impresion.
     */
    'print_params'=>[
        'paper_size'=>[
            'width'=>'210mm',
            'height'=>'290mm',
        ]
    ],
    
    /**
     * Palabras que deben ser excluidas de una busqueda
     */
    'exclude_from_search'=>['de','del','a','o','y'],
    
    /*** End commons ***/
    
    /*** Customers ***/
    /**
     * Identificacion tributaria (CUIT/CUIL) requerida o no requerida.
     * Numero de documento requerido o no requerido.
     */
    'tax_identification_required'=>false,
    'document_number_required'=>false,
     
     /**
     * Categoría Cliente requerido o no requerido.
     */
    'class_customer_required'=>true,
    'category_customer_required'=>true,

    /**
     * El menu de profiles es visible o no
     */
    'profiles_menu'=>true,
    
    /**
     * Campos que se cargan al crear un cliente desde "Sell"
     */
    'embed-fields'=>[
        'name',
        'lastname',
        'email',
//        'document',
//        'phone',
//        'address',
//        'type',
//        'tax_identification',
    ],
    
    /*** End customers ***/
    
    /** BILL **/
    /**
     * Tipo de factura por defecto (bill_type_id)
     */
    'bill_default_type'=>1,
    
    /**
     * Tipo de moneda por defecto (bill_type_id)
     */
    'bill_default_currency'=>1,
    
    /**
     * Mostrar boton "Cerrar factura"?
     */
    'bill_close_button' => true,
    
    /**
     * Botón para "abandonar" factura ("Guardar borrador", etc.)
     */
    'bill_bye_button' => [
        'show' => true,
        'label' => 'Guardar borrador',
        'url' => ['bill/index']
    ],
    
    /**
     * 
     */
    'bill_default_API' => 'FEv2_4',
    
    /**
     * Carga el applet para imprimir y muestra el boton "Imprimir"
     */
    'enable_printer'=>false,
    
    /**
     * Tamanio de pagina de busqueda de productos en "Vender"
     */
    'bill_products_page_size' => 10,
    
    /**
     * Tamanio de pagina de busqueda de clientes en "Vender"
     */
    'bill_customers_page_size' => 10,
    
    /**
     * Permitir actualizar valor de detalle de factura
     */
    'bill_detail_price_updater' => true,
    
    /*** End Bill ***/
    
    /*** PAYMENTS ***/
    'payment_tolerance' => 0.01,
    'account_tolerance' => 2,

    /**
     * Se requiere de un customer al momento de pagar?
     */
    'customer_required' => false,
    
    /*** End Payments ***/
    
    /** IMPORTER **/
    
    /**
     * Id de la unidad por defecto de los productos importados
     */
    'default_unit_id'=>1,
    'import_time_limit'=>1200,
    
    /**** End Importer ***/

    /** AFIP **/
    /**
     * Configuracion para factura electronica
     */
    'einvoice' => [
        'testing'       => false,
        'use-online'    => false,
        'save-calls'    => true
    ],


/****  End AFIP **/
    
    'type_zone'=>[
        'country'=>Yii::t('app','Country'),
        'state'=>Yii::t('app','State'),
        'department'=>Yii::t('app','Department'),
        'locality'=>Yii::t('app','Locality'),
        'zone'=>Yii::t('app','Zone'),
    ],
    
    'map_address'=>true,
    

    'enable_send_bill_email'    => true,

    'mailing' => [
        'factura'=> [
            'layout'=> '@app/views/email-template/layout',
            'from' => 'garciac12@gmail.com',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'garciac12@gmail.com',
                'password' => '',
                'port' => '587',
                'encryption' => 'tls',
            ]
        ],
        'transports' => [
            'Swift_SmtpTransport' => 'Swift SMTP'
        ],
        'layouts' => [
            '@app/views/email-template/westnet/comprobante' => 'Email Westnet',
            '@app/views/email-template/bigway/comprobante' => 'Email Bigway',
            '@app/modules/westnet/notifications/body/content/content' => 'Notificacion Westnet',
            '@app/modules/mobileapp/v1/views/mailing/validation-code' => 'Codigo de validacion',
        ],
        'relation_clases' => [
            'app\modules\sale\models\Company' => 'Empresa'
        ],
    ],

    'accounting' => [
        'movement-implementation-dir' => [
            '@app/modules/accounting/components/impl',
            '@app/modules/westnet/ecopagos/components/accounting/',
            '@app/modules/westnet/ecopagos/components/accounting/',
            '@app/modules/paycheck/components'
        ],
        'countable-implementation-dir' => [
            '@app/modules/sale/models',
            '@app/modules/checkout/models',
            '@app/modules/provider/models',
            '@app/modules/westnet/ecopagos/models',
            '@app/modules/paycheck/models',
        ]
    ],


    /****  End AFIP **/
    
    /*** Companies ***/
    'companies' => [
        'enabled' => true,
        'byUser' => false
    ],
    /*** End Companies ***/

    /*** Files ***/
    'upload_directory' => 'uploads/',
    /*** End Files ***/
    
    /** Agenda enabled/disabled **/
    'agenda_enabled' => true,
    /** End Agenda enabled/disabled **/
    
    /** Ticket enabled/disabled **/
    'ticket_enabled' => true,
    /** end Ticket enabled/disabled **/
    
    /**
     * LOG
     */
    //A partir de este punto, se eliminan los registros antiguos
    'garbageCollectorLimit' => 1000000,
    /**
     * END LOG
     */
    
    /** Behaviors de contrato para el cambio de estado */
    'contract_behaviors_status_change' => 'app\modules\westnet\components\SecureConnectionBehavior',
    'curl_verbose' =>false,

    'tickets_categories_showed' => []
];