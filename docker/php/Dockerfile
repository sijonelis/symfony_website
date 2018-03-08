FROM php:7.1-fpm

VOLUME ["/var/www/html"]

RUN useradd -m php \
&& chown php /var/www/html

RUN add-apt-repository ppa:ondrej/php

RUN apt-get update

RUN php-mysql

RUN apt-get update && apt-get -y install \
    zip unzip php-mysql php-xdebug php-curl php-xml \
    --no-install-recommends \
&& docker-php-ext-install -j$(nproc) intl opcache json pdo pdo_mysql gd mcrypt mbstring zip bcmath \
&& pecl install apcu-5.1.8 \
&& docker-php-ext-enable apcu

# Install Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY ./libs/composer.phar /usr/local/bin/composer

RUN apt-get -y install cron

RUN cd web/
RUN mkdir images
RUN cd images
RUN mkdir tea
RUN cd ..
RUN chmod -R 777 images

#COPY ./cron/*.cron /home/php/
#COPY ./cron/install_cron.sh /home/php/

#RUN chmod a+rwx /home/php/install_cron.sh

USER php

#RUN /home/php/install_cron.sh

USER root

CMD cron && php-fpm