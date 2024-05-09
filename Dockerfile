# Utiliser l'image PHP officielle
FROM php:8.1-cli

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install ctype iconv

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application dans le conteneur
COPY . .

# Installer les dépendances avec Composer
RUN composer install --no-dev --optimize-autoloader

# Exposer le port 8000 pour le serveur Symfony
EXPOSE 8000

# Commande par défaut pour exécuter le serveur Symfony
CMD ["php", "bin/console", "server:run", "0.0.0.0:8000"]
