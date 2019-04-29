FROM eboraas/debian:stable

MAINTAINER Marcelo Briones <ing@marcelobriones.com.ar>

RUN apt-get update && apt-get -y install \
    curl \
    php5 php5-mysql php5-intl php5-gd php5-curl php5-xdebug

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

RUN /usr/sbin/a2ensite default-ssl
RUN /usr/sbin/a2enmod ssl

RUN /usr/sbin/a2dismod 'mpm_*' && /usr/sbin/a2enmod mpm_prefork

RUN sed -i "s/\;date\.timezone =/date\.timezone=\'America\/Argentina\/Mendoza\'/g" /etc/php5/apache2/php.ini
RUN echo "America/Argentina/Mendoza" > /etc/timezone
RUN dpkg-reconfigure --frontend noninteractive tzdata

RUN echo "xdebug.remote_enable=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_handler=dbgp" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_mode=req" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_port=9000" >> /etc/php5/mods-available/xdebug.ini

RUN apt-get -y install barcode make gcc
RUN curl https://ashberg.de/php-barcode/download/files/genbarcode-0.4.tar.gz -o genbarcode.tar.gz
RUN tar -zxvf genbarcode.tar.gz
RUN cd genbarcode-0.4; make; make install; cd ..; rm -Rf genbarcode-0.4

EXPOSE 80
EXPOSE 443

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]