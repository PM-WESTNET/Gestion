<?php
return [
    'mailing' => [
        'layouts' => [
            '@app/views/email-template/layout' => 'Email Westnet' //todo: change to a generic company value
        ],
        'relation_clases' => [
            'app\modules\sale\models\Company' => 'Empresa'
        ]
    ],
];
