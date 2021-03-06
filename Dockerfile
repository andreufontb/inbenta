FROM php:5.5-apache
RUN apt-get update && apt-get install -y git
RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
COPY config/000-default.conf /etc/apache2/sites-enabled/
COPY config/php.ini /usr/local/etc/php/
COPY config/composer /bin/
COPY source/composer.json /var/www/html/
#RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
#        && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
#        && php composer-setup.php --filename=composer --install-dir=bin \
#        && php -r "unlink('composer-setup.php');" \ 
RUN composer install --prefer-source --no-interaction

COPY source /var/www/html