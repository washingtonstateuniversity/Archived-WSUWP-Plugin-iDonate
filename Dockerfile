FROM php:7.1-apache

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
RUN curl -sL https://deb.nodesource.com/setup_5.x | bash
RUN apt-get update && apt-get install -y nodejs

# Install composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
# Make sure we're installing what we think we're installing!
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
&& rm -f /tmp/composer-setup.*

COPY . /var/www/html

#RUN composer install

# Install packages
#RUN npm install

# Install grunt
#RUN npm install grunt --global

#RUN grunt phpcs
#RUN grunt phpunit