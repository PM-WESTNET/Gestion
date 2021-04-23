FROM debian:jessie-slim

MAINTAINER Marcelo Briones <ing@marcelobriones.com.ar>

RUN apt-get update && apt-get -y install \
    wget gnupg2 lsb-release curl mysql-client git \
    php5 php5-mysql php5-intl php5-gd php5-curl php5-xdebug\
	barcode make gcc xvfb xfonts-75dpi fontconfig libxrender1 libjpeg62-turbo vim

	
RUN rm -rf /var/lib/apt/lists/*

RUN curl https://ashberg.de/php-barcode/download/files/genbarcode-0.4.tar.gz -o genbarcode.tar.gz
RUN tar -zxvf genbarcode.tar.gz
RUN cd genbarcode-0.4; make; make install; cd ..; rm -Rf genbarcode-0.4

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.19
COPY composer.json /root/.composer/composer.json
COPY auth.json /root/.composer/auth.json
RUN composer global update

RUN wget https://repo.percona.com/apt/percona-release_latest.jessie_all.deb
RUN dpkg -i percona-release_latest.jessie_all.deb
RUN apt-get update
RUN apt-get install -y percona-xtrabackup-24


#RUN curl -L https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.jessie_amd64.deb -o wkhtmltox_0.12.5-1.xenial_amd64.deb
#RUN dpkg -i wkhtmltox_0.12.5-1.xenial_amd64.deb
COPY wkhtmltopdf.sh /usr/local/bin/wkhtmltopdf.sh
RUN chmod +x /usr/local/bin/wkhtmltopdf.sh

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

RUN /usr/sbin/a2ensite default-ssl
RUN /usr/sbin/a2enmod ssl

RUN /usr/sbin/a2dismod 'mpm_*' && /usr/sbin/a2enmod mpm_prefork

RUN sed -i "s/\;date\.timezone =/date\.timezone=\'America\/Argentina\/Mendoza\'/g" /etc/php5/apache2/php.ini
RUN cp /usr/share/zoneinfo/America/Argentina/Mendoza /etc/localtime

RUN sed -i "s/memory_limit = 128M/memory_limit = 512M/g" /etc/php5/apache2/php.ini
RUN sed -i "s/max_execution_time = 30/max_execution_time = 120/g" /etc/php5/apache2/php.ini
RUN sed -i "s/post_max_size = 8M/post_max_size = 50M/g" /etc/php5/apache2/php.ini

RUN echo "xdebug.remote_enable=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_handler=dbgp" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_mode=req" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_port=9000" >> /etc/php5/mods-available/xdebug.ini

#Config apache2 and sites-available
RUN cat /etc/apache2/mods-available/rewrite.load
RUN a2enmod rewrite
RUN ls -al /etc/apache2/mods-enabled/rewrite.load
RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

COPY gestion.conf /etc/apache2/sites-available/gestion.conf
RUN chmod +x /etc/apache2/sites-available/gestion.conf
RUN a2ensite gestion

EXPOSE 80
EXPOSE 443

RUN echo "export PATH=$PATH:\"/var/www/html/vendor/bin\"" >> /root/.bashrc

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]