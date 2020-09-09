## Simple Dockerfile to build PMSF (develop branch)
# - The base image can be found here: https://github.com/thecodingmachine/docker-images-php
# - Inside the container, the content of this git repo lives in /var/www/html/
## You have to mount your configs into the container:
# - mount config.php to /var/www/html/config/config.php
# - mount access-config.php to /var/www/html/config/access-config.php
# - Also mount every other configuration file necessary into the according directory.

FROM thecodingmachine/php:7.4-v3-apache-node10

RUN git clone -b develop https://github.com/pmsf/PMSF.git .
RUN composer install
RUN npm install
RUN npm audit fix
RUN npm run build