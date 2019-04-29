<?php

if (!is_dir('web/uploads/certificates')) {
    mkdir('web/uploads/certificates', 0777, true);
}
if (!is_dir('web/uploads/keys')) {
    mkdir('web/uploads/keys', 0777, true);
}

copy('modules/invoice/components/einvoice/afip/certs/23298348004.crt', 'web/uploads/certificates/23298348004.crt');
copy('modules/invoice/components/einvoice/afip/certs/23298348004.key', 'web/uploads/keys/23298348004.key');

$dbhelper = new TestDbHelper();
$I = new WebGuy($scenario);
$dbhelper->initializeDb($I);
