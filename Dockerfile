FROM khromov/alpine-nginx-php8:latest

USER root

# Override nginx config from base image
RUN rm /etc/nginx/nginx.conf
COPY config/nginx.conf /etc/nginx/nginx.conf

# Remove default folder
RUN rm -Rfv /var/www/html
COPY --chown=nobody ./db /var/www/db
COPY --chown=nobody ./src /var/www/src
COPY --chown=nobody config.sample.php /var/www/config.php
COPY composer.json composer.lock /var/www/
RUN cd /var/www/ && composer install --no-dev --no-cache
RUN chown -R nobody.nobody /var/www/src

USER nobody