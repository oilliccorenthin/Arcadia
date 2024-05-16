# Installation avec Docker

## Prérequis
Avant de commencer, assurez-vous d'avoir Docker et Docker Compose installés sur votre système.

## Instructions
1. **Cloner le Projet**
   ```bash
   git clone https://github.com/oilliccorenthin/Arcadia.git
   cd projet

2. **Configuration de l'Environnement**
   Assurez-vous que votre fichier .env est configuré correctement, en particulier les variables liées à la base de données.

3. **Construction des Conteneurs Docker**
   docker-compose up --build

4. **Accès à l'Application**
   Une fois les conteneurs Docker démarrés, l'application Symfony sera accessible à l'adresse suivante dans votre navigateur :
      http://localhost:8080

3. **Arrêt des Conteneurs**
   Pour arrêter les conteneurs Docker, utilisez la combinaison de touches Ctrl + C dans votre terminal, puis exécutez :
      docker-compose down
