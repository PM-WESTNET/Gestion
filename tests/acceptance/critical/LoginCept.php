<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that login works');

$I->amGoingTo('try to login with empty credentials');
$I->login('', '');
$I->expectTo('see validations errors');
$I->see('Username no puede estar vacío');
$I->see('Clave no puede estar vacío.');

$I->amGoingTo('try to login with wrong credentials');
$I->login('admin', 'wrong');
$I->expectTo('see validations errors');
$I->see('Combinación de nombre de usuario y contraseña incorrecta.');

$I->amGoingTo('try to login with correct credentials');
$I->login('user', 'user');
$I->dontSee('Combinación de nombre de usuario y contraseña incorrecta.');
$I->see('user');

$I->click("//span[contains(@class,'user')]/..");
$I->click('Cambiar clave');

$I->fillField('ChangeOwnPasswordForm[current_password]', 'user');
$I->fillField('ChangeOwnPasswordForm[password]', 'password');
$I->fillField('ChangeOwnPasswordForm[repeat_password]', 'password');

$I->click(".//*[@id='user']/div[4]/div/button");
$I->wait(1);
$I->waitForText('La clave ha sido modificada');

$I->click("//span[contains(@class,'user')]/..");
$I->click('Logout');

$I->login('user', 'user');
$I->see('Combinación de nombre de usuario y contraseña incorrecta.');

$I->login('user', 'password');
$I->dontSee('Combinación de nombre de usuario y contraseña incorrecta.');
$I->see('user');

$I->click("//span[contains(@class,'user')]/..");
$I->click('Cambiar clave');

$I->fillField('ChangeOwnPasswordForm[current_password]', 'password');
$I->fillField('ChangeOwnPasswordForm[password]', 'user');
$I->fillField('ChangeOwnPasswordForm[repeat_password]', 'user');

$I->click(".//*[@id='user']/div[4]/div/button");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}

$I->see('La clave ha sido modificada');
