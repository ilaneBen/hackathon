# Hackathon - Application d'Audit de Code

La sécurité des développements est une préoccupation majeure, et bien que l'intelligence artificielle ait fait des progrès significatif.
L'expertise humaine reste irremplaçable dans ce domaine.
Cependant, de nombreux outils d'analyse de code peuvent être des alliés précieux dans la détection d'erreurs courantes.
Notre application, développée lors de ce hackathon, vise à exploiter au mieux ces outils et à fournir une solution complète et conviviale.


## Fonctionnalités

L'utilisateur a la possibilité de définir l'URL du dépôt GitHub public de son projet PHP dans un formulaire et de choisir les outils d'analyse qu'il souhaite utiliser. 
Ensuite, l'application génère un rapport détaillé après avoir examiné le code du projet. L'utilisateur est informé par e-mail de l'avancement de l'analyse, et il peut consulter le rapport directement sur l'application ou le télécharger au format PDF.
Pour garantir la convivialité de l'application, plusieurs outils d'analyse sont intégrés, couvrant divers aspects du code, tels que :
- Composer, 
- PHPStan, 
- PHPCS, 
- Style Line, 
- Eslint, 
- NPM audit  
- Yarn audit.

## Technologies Utilisées

- **Frontend:** Développé avec React
- **Backend:** API Symfony en PHP
- **Traitement Asynchrone:** Utilisation de Queues
- **Notifications:** Envoi d'e-mails transactionnels pour informer l'utilisateur des étapes de traitement de son projet

## Outils d'Analyse Intégrés

- **Analyse Composer:**
    - Composer Audit

- **Analyse PHP:**
    - PHPStan
    - PHPCS

- **Analyse CSS & SCSS:**
    - Style Line

- **Analyse JS:**
    - Eslint

- **Audit NPM & Yarn:**
    - NPM Audit
    - Yarn Audit

## Comment Utiliser le Projet

Suivez ces étapes simples pour mettre en place et utiliser le projet localement :

1. Clonez le dépôt.
2. Exécutez les commandes suivantes pour installer les dépendances PHP et JavaScript :
- `composer install`
- `npm install`
3. Configuration de l'Environnement Local:
- Créez un fichier .env.local à la racine du projet.
- Ajoutez vos informations personnelles telles que les clés API, les identifiants de base de données, etc.
4. Création de la Base de Données exécutez la commande suivante pour créer la base de données :
- `php bin/console doctrine:database:create`
5. Lancez les migrations pour mettre en place la structure de la base de données :
- `php bin/console doctrine:migrations:migrate` 
6. Démarrez le serveur Symfony avec la commande :
`symfony serve`
Surveillez les modifications des fichiers JS et CSS avec la commande :
- `npm run watch`
## Configuration du Fichier env.local :
Copiez le contenu du fichier .env fourni en exemple dans le fichier .env.local.
Mettez à jour les informations dans le fichier .env.local avec vos clés d'API, identifiants de base de données, etc.
Profitez pleinement de l'expérience d'audit de code! Assurez-vous de personnaliser correctement votre fichier .env.local pour garantir le bon fonctionnement du projet.


## Comment Utiliser le Projet

1. Clonez le dépôt.
2. Installez les dépendances en exécutant les commandes suivantes:
    - `composer install`
    - `npm install`
3. Lancez le projet en utilisant les commandes suivantes:
    - `symfony serve`
    - `npm run watch`

## Création de Données de Test (Fixture)

Vous avez deux options pour la création de données de test:
1. Utilisez les fixtures existantes commandes:
    - `symfony console app:create-all` (crée tous les éléments)
    - `symfony console app:create-jobs`
    - `symfony console app:create-projects`
    - `symfony console app:create-rapports`
    - `symfony console app:create-users`
2. Créez vos propres données en utilisant le projet.

Profitez de l'expérience d'audit de code complète et n'hésitez pas à contribuer et améliorer cette application pendant ce hackathon!