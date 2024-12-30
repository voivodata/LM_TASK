FROM php:8.2-fpm
RUN echo 'alias ll="ls -la"' >> ~/.bashrc
RUN apt-get update && apt-get upgrade && apt-get install -y vim git unzip
RUN docker-php-ext-install pdo_mysql
# RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony
ENV PATH="/usr/local/bin:$PATH"
RUN mkdir -p /var/cache/symfony && chmod -R 777 /var/cache/symfony
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer