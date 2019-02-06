FROM php:7.2-apache
ADD index.php /var/www/html/index.php
ADD secret.php /var/www/html/secret.php
ADD secret3.php /var/www/html/secret3.php
ADD level1.php /var/www/html/level1.php
ADD level2.php /var/www/html/level2.php
ADD level3.php /var/www/html/level3.php
EXPOSE 80
WORKDIR /var/www/html
