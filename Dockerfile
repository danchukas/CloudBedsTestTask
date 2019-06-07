FROM php:7.2-apache

MAINTAINER danchukas

ARG project_root

WORKDIR ${project_root}

# for next calls apt-get install
RUN apt-get update --fix-missing

# for speed up
RUN docker-php-ext-install opcache

# for enable XDEBUG
RUN yes | pecl install xdebug
RUN echo "\
zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so) \n\
xdebug.remote_enable=on \n\
xdebug.remote_host=gateway.docker.internal \n\
xdebug.remote_log=${project_root}/logs/remote/debug.log \n\
xdebug.profiler_enable=0 \n\
xdebug.profiler_output_dir=${project_root}/logs/profiler \n\
xdebug.profiler_output_name=cachegrind.out \n\
" > /usr/local/etc/php/conf.d/xdebug.ini

# install composer
RUN apt-get install -y curl git unzip \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# instead warning exist
ENV COMPOSER_ALLOW_SUPERUSER 1
# otherwise out of memory
ENV COMPOSER_MEMORY_LIMIT -1

# for connect with Mysql from php
RUN docker-php-ext-install mysqli

# Include file of apache config from project to apache of this image.
# Command "ln" create symbolic link (analog shortcut) on file.
# It is better way because anybody who list apache configs will understand
# where editable source is situated.
RUN ln -sf ${project_root}/apache.conf /etc/apache2/sites-enabled/cloudbeds.test.v1.conf \
# Fix for error: Invalid command 'RewriteEngine', perhaps misspelled or
#                defined by a module not included in the server configuration
# For remove extra layer and for a tiny speed increase used "&&" after previous "Run" instead new "RUN".
&& a2enmod rewrite \
# For available send headers from virtual host config
&& a2enmod headers

# for run sql file by CircleCi command
RUN apt-get install -y mysql-client

# Becouse in case without it we had error during build child docker image.
# E: Could not open file /var/lib/apt/lists/deb.debian.org_debian_dists_buster_main_binary-amd64_Packages.diff_Index - open (2: No such file or directory)
RUN rm -r /var/lib/apt/lists/*
