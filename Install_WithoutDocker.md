# Guide de déploiement de l'application en local sans Docker

    Ce guide vous aidera à déployer l'application en local sur votre machine. Assurez-vous de suivre toutes les étapes pour un déploiement réussi.

## Prérequis

    Avant de commencer, assurez-vous d'avoir installé les éléments suivants sur votre machine :

    - [PHP](https://www.php.net/downloads)
    - [Composer](https://getcomposer.org/download/)
    - [MySQL](https://www.mysql.com/downloads/)
    - [Symfony CLI](https://symfony.com/download)

## Étapes de déploiement

1. **Cloner le dépôt Git**

    Clonez le dépôt Git de l'application sur votre machine locale :
        git clone https://github.com/oilliccorenthin/Arcadia.git


2. **Installation des dépendances**

    Accédez au répertoire de votre projet Symfony et installez les dépendances en exécutant la commande suivante :
        composer install

3. **Création de la base de données**

    Créez la base de données en exécutant la commande Symfony suivante :
        php bin/console doctrine:database:create

4. **Importation de la structure de la base de données**

    Importez la structure de la base de données à partir du fichier SQL `database_structure.sql` en utilisant la méthode appropriée pour votre système de gestion de base de données.

    **Pour MySQL :**
        mysql -u username -p database_name < database_structure.sql

    Remplacez `username` par votre nom d'utilisateur MySQL et `database_name` par le nom de votre base de données.

    **Pour PostgreSQL :**
        psql -U username -d database_name -a -f database_structure.sql
    
    Remplacez `username` par votre nom d'utilisateur PostgreSQL et `database_name` par le nom de votre base de données.

    Assurez-vous d'avoir créé au préalable la base de données avec le nom approprié dans votre système de gestion de base de données.

5. **Lancement du serveur local**

    Lancez le serveur de développement Symfony en exécutant la commande suivante :
        symfony server:start


6. **Accès à l'application**

    Ouvrez votre navigateur web et accédez à l'URL suivante pour accéder à votre application :
        http://localhost:8000


