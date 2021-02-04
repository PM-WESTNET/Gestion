<?php

return [
    
    'sizes' => [
        'default' => [640, 480],
        's' => [300, 300],
        'm' => [640, 480],
        'l' => [1280, 720],
    ],
    
    /**
     * Media tipo imagen.
     * Todos estos valores son requeridos; si alguno no se especifica, el 
     * componente Image no funcionara
     */
    'Image' => [
        'extensions' => 'png, jpg',
    ]
];