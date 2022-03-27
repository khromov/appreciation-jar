FROM khromov/alpine-nginx-php8:latest

USER root

COPY composer.json composer.lock /var/www/project/
COPY src/ /var/www/project/src
RUN rm -rfv /var/www/html
RUN ln -s /var/www/project/src/ /var/www/html
RUN ls -la /var/www/html
RUN cd /var/www/project && composer install

USER nobody