FROM php:7.1-apache
#FROM php:7.0-fpm
LABEL version="1.0"
LABEL description="WSU Foundation Online Giving WordPress Plugin"
LABEL maintainer="Jared Crain <jared.crain@wsu.edu>"

RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
	&& docker-php-ext-install -j$(nproc) iconv mcrypt \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

# Install curl
RUN apt-get update && apt-get install -y curl

# Install zip (get around git dependancy)
RUN apt-get update && apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip

# Install Node
#RUN apt-get update && apt-get install -y nodejs npm
RUN curl -sL https://deb.nodesource.com/setup_5.x | bash
RUN apt-get update && apt-get install -y nodejs

# Install composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
# Make sure we're installing what we think we're installing!
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
&& rm -f /tmp/composer-setup.*

VOLUME [ "/var/www/html/wp-content/plugins" ]
#VOLUME /var/www/html/wp-content/plugins/WSUWP-Plugin-iDonate
COPY . /var/www/html/wp-content/plugins/WSUWP-Plugin-iDonate
WORKDIR /var/www/html/wp-content/plugins/WSUWP-Plugin-iDonate

RUN composer install


# use changes to package.json to force Docker not to use the cache
# when we change our application's nodejs dependencies:
COPY ./package.json /tmp/package.json
RUN cd /tmp && npm install
RUN mkdir -p /opt/app && cp -a /tmp/node_modules /opt/app/

# From here we load our application's code in, therefore the previous docker
# "layer" thats been cached will be used if possible
WORKDIR /opt/app
ADD . /opt/app

COPY . /var/www/html/wp-content/plugins/WSUWP-Plugin-iDonate