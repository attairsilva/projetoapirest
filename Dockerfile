
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip zip git \
    pkg-config \
    wget \
    unzip \
    dos2unix \
    curl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get install -y netcat-openbsd  

# Modo Reescrita do Apache
RUN a2enmod rewrite

# Personalização do Apache
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Entrypoint personalizado
COPY docker-entrypoint.sh /usr/local/bin
# Aguardar banco personalizado
COPY aguardar-banco.sh /usr/local/bin

# Torna Entrypoint e Aguarda Banco Executável
RUN chmod +x /usr/local/bin/docker-entrypoint.sh /usr/local/bin/aguardar-banco.sh

# Copia os arquivos do projeto para o contêiner
COPY src/ /var/www/html

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

# Corrigir scripts com CRLF
RUN dos2unix /usr/local/bin/docker-entrypoint.sh /usr/local/bin/aguardar-banco.sh

# Instale o Composer (corrigido)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN composer install

# Instale league/flysystem-aws-s3-v3
# RUN composer require league/flysystem-aws-s3-v3

# Instala as dependências do Laravel
RUN composer install --no-dev --no-interaction --prefer-dist && composer require fakerphp/faker --dev
# Instala as dependências do Laravel
RUN composer require fakerphp/faker --dev


# Ajudar Horario Container
RUN ln -snf /usr/share/zoneinfo/America/Cuiaba /etc/localtime && echo "America/Cuiaba" > /etc/timezone

# Define o ServerName diretamente no apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# G.env exista ANTES dos comandos do artisan
RUN if [ ! -f ".env" ]; then \
        if [ -f ".env.renomeie" ]; then \
            echo "Copiando .env.renomeie para .env"; \
            cp .env.renomeie .env; \
        else \
            echo "Arquivo .env.renomeie não encontrado!"; \
            exit 1; \
        fi \
    ; fi


RUN php artisan key:generate

# Exponha a porta 80
EXPOSE 80

# Define o entrypoint  - script personalizado
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
# Inicie o Apache 
# CMD ["apache2-foreground"]