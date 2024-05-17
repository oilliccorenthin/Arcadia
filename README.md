# ARCADIA

## Description
ARCADIA est un projet Symfony prêt à l'emploi, conçu pour simplifier le processus de développement et de déploiement d'applications Symfony.

## Prérequis
Avant de commencer, assurez-vous d'avoir Docker et Docker Compose installés sur votre système.


## Instructions
1. **Cloner le Projet**
   ```bash
   git clone https://github.com/oilliccorenthin/Arcadia.git
   cd Arcadia

2. **Monter les images docker et lancer l'environnement**
   ```bash
   docker-compose pull
   docker-compose up

3. **Entrez dans le container disposant de l'app**
   ```bash
   docker-compose exec -it www bash

4. **Entrez dans le container disposant de l'app**
    ```bash
   composer update

5. **Créer la base de données**
    ```bash
   php bin/console doctrine:database:create

6. **Générer les migrations et migrer la base de données**
    ```bash
   php bin/console doctrine:migration:migrate

6. **Charger les fixtures**
    ```bash
   php bin/console doctrine:fixtures:load

## Créateur
- OILLIC Corenthin (https://github.com/oilliccorenthin)

