ACTUALIZACION DEL MOTOR DE MARIADB DENTRO DEL SERVIDOR 

pasos para la instalacion en caso de no tenerlo instalado:

_primero ingresamos al servidor por ssh utilizando las credenciales proporcionadas

_nos paramos en la carpeta /etc/apt

_dentro del archivo sources.list buscamos el link al repositorio viejo, lo copiamos y lo eliminamos con el siguiente comando 

    add-apt-repository --remove <LINK COPIADO>

_luego agregamos el link del nuevo repositorio con 

    sudo apt-get install software-properties-common
    sudo add-apt-repository <LINK NUEVO>

****    EN CASO DE ERROR     ****
el error mas comun en este caso es el de la llave GPG del repositorio, lo que esta hace es demostrarle al SO que es un link valido.
la mejor forma de arreglar este error es buscando la llave GPG original y agregarla.

de no ser posible lo anterior, podemos editar el archivo sources.list y agregar "[trusted=yes]" entre "deb" y el link del repositorio
¡¡¡ES IMPORTANTISIMO CHEQUEAR QUE EL LINK SEA VALIDO SI VAMOS A HACER ESTO!!!


_por ultimo hay que reinstalar junto a la instalacion segura de mysql

    sudo apt install mariadb-server
    mysql_secure_installation

_leer con atencion cada paso de la instalacion segura de mysql para no eliminar o agregar configuraciones innecesarias





fuente:
https://mariadb.com/kb/en/installing-mariadb-deb-files/#updating-the-mariadb-apt-repository-to-a-new-major-release
