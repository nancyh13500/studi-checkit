FROM php:8.2-apache

# Activer mod_rewrite et config basique Apache
RUN a2enmod rewrite

# Copier les sources dans le dossier web d'Apache
WORKDIR /var/www/html
COPY . /var/www/html

# Droits simples (environnement de dev)
RUN chown -R www-data:www-data /var/www/html

# Exposer le port HTTP
EXPOSE 80


