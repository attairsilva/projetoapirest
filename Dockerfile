
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip zip git \
    pkg-config \
    wget \
    unzip \
    curl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Minio Client (mc)
# RUN wget https://dl.min.io/client/mc/release/linux-amd64/mc -O /usr/local/bin/mc \
# && chmod +x /usr/local/bin/mc

# Modo Reescrita do Apache
RUN a2enmod rewrite

# Personalização do Apache
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Entrypoint personalizado
COPY docker-entrypoint.sh /usr/local/bin/

# Torna Entrypoint executável
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# RUN wget https://dl.min.io/client/mc/release/linux-amd64/mc -O /usr/local/bin/mc \
#     && chmod +x /usr/local/bin/mc \
#     && ls -l /usr/local/bin/
    
# Copia os arquivos do projeto para o contêiner
COPY src/ /var/www/html

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html

# Define permissões
# RUN mkdir -p /var/www/html/.mc && \
#     chown -R root:root /var/www/html/.mc && \
#     chmod 700 /var/www/html/.mc

WORKDIR /var/www/html

# Instale o Composer (corrigido)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN composer install

# Instale league/flysystem-aws-s3-v3
# RUN composer require league/flysystem-aws-s3-v3

# Instala as dependências do Laravel
RUN composer install --no-dev --no-interaction --prefer-dist && composer require fakerphp/faker --dev

# Limpa o cache do autoload
RUN composer dump-autoload

# Ajusta permissões para a pasta de armazenamento e cache
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Habilita o mod_rewrite do Apache (necessário para Laravel)
# RUN a2enmod rewrite

# Ajudar Horario Container
RUN ln -snf /usr/share/zoneinfo/America/Cuiaba /etc/localtime && echo "America/Cuiaba" > /etc/timezone

# Define o ServerName diretamente no apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Exponha a porta 80
EXPOSE 80

# Define o entrypoint  - script personalizado
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
# Inicie o Apache 
# CMD ["apache2-foreground"]