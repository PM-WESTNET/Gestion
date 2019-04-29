<?php
return [
    'mailing' => [
        'layouts' => [
            '@app/views/email-template/layout' => 'Email Westnet'
        ],
        'relation_clases' => [
            'app\modules\sale\models\Company' => 'Empresa'
        ]
    ],
];
