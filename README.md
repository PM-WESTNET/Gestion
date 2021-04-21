# Arya (GestionISP)

[//]: # (Add a short  description here)

# [Arya Documentation](https://docs.google.com/document/d/1XQ9qM9zJ1bhrtbTwXQV-JC0yCKlHt0WS7KMXf0CussI/edit#)
[//]: # (You need to be provided access beforehand)

# Arya (GestionISP) Install:
[//]: # (Setups for common operating systems)


## Ubuntu 20.04 (or windows)
-----------------

#### *Requirements*

- Composer
- Docker


### *Step-by-step:*

#### Go to the projects folder (wherever you whant to clone it)

    $   cd /var/www
#### Clone the repo
    $   git clone https://github.com/westnet1/ispGestioner.git gestion
    
#### Up the container services and dependencies
    $   cd docker
    $   docker-compose up -d

#### Go to the project's base folder and run this commands
*you can copy-paste this commands*

    $
    cp config/db.sample.php config/db.php;
    cp config/web.sample.php config/web.php;
    cp config/params.sample.php config/params.php;
    cp config/console.sample.php config/console.php;
    cp web/index-test.php web/index.php;
    
#### Replace with real data the created files
Inside the config folder, you will have to change the data of the connections that are described. 
Do as follows:
- The **host** should be setted for the container name. (previously it was either an IP or localhost)
- The **dbname** should be setted for the actual database name like "gestion_westnet" (Example at the time of writing this)
- The **password** should be setted also (ask)

replace "'password' => '*********'" "'password' => '[NEWPASSWORD]'" -- config/db.php

*Nota importante* => En el archivo **index.php** creado, remplazar la linea:

    $config = require __DIR__ . '/../config/test.php';

por:

    $config = require __DIR__ . '/../config/web.php';



Asegurarse de ingresar la direccion correcta en el archivo db.php en config, y verificar el nombre de las tablas


#### Create a custom docker folder to compose locally on your computer
(could be called "gestion_westnet" for example, and should be ignored by git)


**to-do:**  
1. describe the changes on the docker folder
On the 
1. add the just created docker folder to .gitignore: "docker\gestion_westnet" is already ignored in the gitignore by default
1. describe how to connect to database
1. describe how to dump the SQL script and the tools required for that; workbench and client
1. describe how to connect to database


----------------------------------------------------------------------------------------------------------------
Para Ubuntu 16.04
-----------------

$ sudo apt-get update; sudo apt-get -y upgrade; sudo apt-get -y dist-upgrade; sudo apt-get -y autoremove; sudo reboot

$ sudo apt-get -y install git apache2 gedit nano php php-mysql php-intl php-gd php-xml php-mbstring php-curl libapache2-mod-php

$ sudo service apache2 restart

$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php composer-setup.php; php -r "unlink('composer-setup.php');"; mkdir ~/.composer; mv composer.phar ~/.composer/composer
$ echo "PATH=\$PATH:~/.composer" >> ~/.bashrc; echo "PATH=\$PATH:~/.composer/vendor/bin" >> ~/.bashrc; export PATH=$PATH:~/.composer:~/.composer/vendor/bin
$ composer global require "fxp/composer-asset-plugin:*"

$ mkdir ~/develop; cd ~/develop
$ git clone https://bitbucket.org/quomateam/westnet.git
$ cd westnet
$ mkdir web/uploads/certificates
$ cp modules/invoice/components/einvoice/afip/certs/CarlosGarcia.crt web/uploads/certificates/certificate55f2e06f12290.crt
$ mkdir web/uploads/keys
$ cp modules/invoice/components/einvoice/afip/certs/CarlosGarcia.key web/uploads/keys/key55f2e06f12912.key
$ mkdir runtime; chmod 777 runtime; mkdir web/assets; chmod 777 web/assets

$ composer install

$ sudo apt-get -y install barcode make gcc
$ curl https://ashberg.de/php-barcode/download/files/genbarcode-0.4.tar.gz -o genbarcode.tar.gz
$ tar -zxvf genbarcode.tar.gz
$ cd genbarcode-0.4; make; make install; cd ..; rm -Rf genbarcode-0.4

$ sudo apt-get -y install mysql-server mysql-client
(DON'T LEAVE mysql root password to empty yet, it won't work later!!!)

$ cp config/db.sample.php config/db.php
$ replace "'password' => '*********'" "'password' => '[NEWPASSWORD]'" -- config/db.php

$ sudo ln -s /home/[user]/develop/westnet /var/www/html/westnet

$ firefox http://localhost/westnet/web/

Run tests
---------

$ sudo apt-get install default-jre chromium-browser
(Chrome and chromium are wuite similar but not the same)

$ composer global require "codeception/codeception:*"

$ cp tests/acceptance.suite.sample.yml tests/acceptance.suite.yml
$ cp tests/_config.sample.php tests/_config.php

# Download and run selenium stand slone server
# http://docs.seleniumhq.org/download/
# Download Chrome drivers and leave in same directory as selenium
# https://sites.google.com/a/chromium.org/chromedriver/downloads

$ java -jar selenium-server-standalone-3.4.0.jar

$ codecep run acceptance


<?php
Gestion::$api_url = 'https://gestion.westnet.com.ar/index.php?r=westnet/api/';
Gestion::$api_headers = [ 'Content-Type: application/json', 'Authorization: Basic YXBpX3VzcjozPEFHTDExQ0g4WD8=', 'Cache-Control: no-cache' ];

Model::$db = new PDOProxy ('pgsql:host=127.0.0.1;dbname=westnet', 'westnet', 'rkh7*8lpa9!1.', array (PDO::ATTR_TIMEOUT => 10));