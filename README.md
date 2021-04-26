<<<<<<< HEAD
ISPGestioner Documentation
================================
https://docs.google.com/document/d/1XQ9qM9zJ1bhrtbTwXQV-JC0yCKlHt0WS7KMXf0CussI/edit#
=======
# ISPGestion
>>>>>>> gbermudez

[//]: # (Add a short  description here)

<<<<<<< HEAD
install
================================
=======
# [ISPGestion Documentation](https://docs.google.com/document/d/1XQ9qM9zJ1bhrtbTwXQV-JC0yCKlHt0WS7KMXf0CussI/edit#)
[//]: # (You need to be provided access beforehand)
>>>>>>> gbermudez

# ISPGestion Install:
[//]: # (Setups for common operating systems)


## Ubuntu 20.04 (or windows)
-----------------

#### *Requirements*

- @Composer
- @Docker


## - *Step-by-step:*

#### Go to the projects folder (wherever you whant to clone it)

    $   cd /var/www
#### Clone the repo
    $   git clone https://github.com/westnet1/ispGestioner.git gestion

#### Parameterizing for different companies

- first, make a copy of the docker folder.


    (replace westnet with the company name)

        $   cp -R docker_sample docker_gestion_westnet;

    (The ignored folder prefix in .gitignore is " docker_gestion_* ")
#### Replace data in docker-compose.yml file

change each service "container_name" to something like "gestion-wn-web" (in the case of westnet's web container)


#### Up the container services and dependencies
    $   cd docker_gestion_westnet;
    $   docker-compose up -d;

#### - Make @Composer install all dependencies 
**(up the containers first!!)**
From the console, run this commands (Adapt to fit the containers names):

- open this container's terminal

        $   docker exec -it gestion-web sh

- inside (#) go to the html folder
    
        $   cd /var/www/html

- run Composer to install dependencies    
    
        $   composer install

- The terminal will output a GitHub URL in which you have to check every box and generate a personal Token
    (paste the token)



#### Go to the project's base folder and run this commands
*you can copy-paste this commands*

Create copies of config files, web files and docker files. (extremely important)

    cp config/db.sample.php config/db.php;
    cp config/web.sample.php config/web.php;
    cp config/params.sample.php config/params.php;
    cp config/console.sample.php config/console.php;
    cp web/index-test.php web/index.php;
    cp docker/web-server/gestion.sample.conf docker/web-server/gestion.conf;
    

#### Replace in the config folder with real data for connections
Inside the config folder, you will have to change the data of the connections that are described. 
Do as follows:
- The **host** should be setted for the container name. (previously it was either an IP or localhost, change for smt like gestion-wn-data)
- The **dbname** should be setted for the actual database name (like gestion_westnet.., etc)
- The **password** should be setted also (ask)
*use the "replace all ocurrences" option of your texteditor*

replace "'password' => '*********'" "'password' => '[NEWPASSWORD]'" -- config/db.php


#### Replace in the web folder configuration file name
*Nota importante* => En el archivo **index.php** creado, remplazar la linea:

    $config = require __DIR__ . '/../config/test.php';

por:

    $config = require __DIR__ . '/../config/web.php';


#### Add gestion to the list of local-hosts
- Go to /etc folder in your linux distr system
- edit the "hosts" file with:

        $   sudo nano hosts
    (or any text editor)

- Add a line with Gestion's ip and hostname:

        127.0.0.1   gestion_westnet.local

**(Change to the actual's company name!)**

#### Change the endpoint's IP for easy access to containers (Portainer)

*localhost*

#### If you encounter the error exception: 
"failed to create directory: permission denied"

- Move the **assets** folder inside the **web** folder.
        
        $   cp -R assets web/assets;

- Run a CHMOD:

        $   sudo chmod -R 777 gestion_project_folder/
        (This will recursively add permissions to write)


- Go inside the project folder and run:

        $   git config core.fileMode false
        (Git detects the file changes in writability, this will exclude that from being pushed to remote)

####
#### Rebuild docker.

        $   sudo docker-compose build

## >>At this point you should be able to see Gestion working.



----------------------------------------------------------------------------------------------------------------
# @old documentation:

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