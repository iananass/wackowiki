FROM ubuntu/apache2

RUN apt update && apt install -y git php libapache2-mod-php php-ctype php-gd php-bcmath php-json php-mbstring php-intl php-iconv php-mysql && a2enmod rewrite

ARG SERVER_NAME=vtm-iananass.xyz
ARG ADMIN_EMAIL=iananass@gmail.com

RUN echo '<VirtualHost *:80>'                                    > /etc/apache2/sites-available/000-default.conf  \
 && echo "    ServerName ${SERVER_NAME}"                         >> /etc/apache2/sites-available/000-default.conf \
 && echo '    <Directory /var/www/html/>'                        >> /etc/apache2/sites-available/000-default.conf \
 && echo '        Options Indexes FollowSymLinks MultiViews'     >> /etc/apache2/sites-available/000-default.conf \
 && echo '        AllowOverride All'                             >> /etc/apache2/sites-available/000-default.conf \
 && echo '        Require all granted'                           >> /etc/apache2/sites-available/000-default.conf \
 && echo '    </Directory>'                                      >> /etc/apache2/sites-available/000-default.conf \
 && echo '</VirtualHost>'                                        >> /etc/apache2/sites-available/000-default.conf

ARG INSTALL_PATH=/var/www/html

COPY --chmod=777 ./src ${INSTALL_PATH}

