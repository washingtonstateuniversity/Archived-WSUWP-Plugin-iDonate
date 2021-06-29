FROM php:7.4-apache

LABEL version="1.1"
LABEL description="WSU Foundation Online Giving WordPress Plugin"
LABEL maintainer="Blair Lierman <blair.lierman@wsu.edu>"

# Install subversion, curl, gnupg, node, npm and gulp
RUN apt-get update
RUN apt-get install -y subversion
RUN apt-get install -y curl
RUN apt-get install -y gnupg
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g gulp

# Install zip (for composer)
RUN apt-get install -y zlib1g-dev libzip-dev
RUN docker-php-ext-install zip

# Install composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
# Make sure we're installing what we think we're installing!
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
&& rm -f /tmp/composer-setup.*

COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-interaction

# Install phpcs and export the path
ENV PATH="~/.composer/vendor/bin:./vendor/bin:${PATH}"
RUN composer global require "squizlabs/php_codesniffer=*"

#PHPUNIT
RUN composer global require "phpunit/phpunit=6.*"
ENV PATH /root/.composer/vendor/bin:$PATH
RUN ln -s /root/.composer/vendor/bin/phpunit /usr/bin/phpunit

# Install packages
RUN npm install

EXPOSE 8000

# TODO: Environment variable to run in dev?
