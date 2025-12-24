FROM php:8.2-apache

# Installer les dépendances système et l'extension PDO MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite et config basique Apache
RUN a2enmod rewrite

# Copier les sources dans le dossier web d'Apache
WORKDIR /var/www/html
COPY . /var/www/html

# Droits simples (environnement de dev)
RUN chown -R www-data:www-data /var/www/html

# Exposer le port HTTP
EXPOSE 80


