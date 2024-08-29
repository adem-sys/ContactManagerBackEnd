# ContactManagerBackEnd

## Application de Gestion des Contacts - Backend (Symfony)

### Description

Ce projet Symfony fournit le backend pour la gestion des contacts, incluant des API pour effectuer des opérations CRUD sur les contacts.

### Prérequis

- PHP (version >= 8.0)
- Composer
- MySQL

### Installation

1. Clonez le dépôt :
    ```bash
    git clone https://github.com/adem-sys/ContactManagerBackEnd.git
    ```

2. Installez les dépendances avec Composer :
    ```bash
    cd ContactManagerBackEnd
    composer install
    ```

3. Renommez le fichier `.env.example` en `.env` et configurez les paramètres de connexion à la base de données MySQL. Assurez-vous de mettre à jour les variables suivantes dans le fichier `.env` :
    ```bash
    DATABASE_URL=mysql://<username>:<password>@<host>:<port>/<database>
    ```

4. Exécutez les migrations pour créer la base de données et les tables nécessaires :

    - **Générez une migration** pour créer les fichiers de migration basés sur les changements apportés aux entités :
      ```bash
      php bin/console make:migration
      ```

    - **Exécutez les migrations** pour appliquer les changements à la base de données :
      ```bash
      php bin/console doctrine:migrations:migrate
      ```

    - **Vérifiez l'état des migrations** et assurez-vous que tout est en ordre :
      ```bash
      php bin/console doctrine:migrations:status
      ```

5. Démarrez le serveur Symfony :
    ```bash
    symfony server:start
    ```

### Tests

1. Pour exécuter les tests unitaires, utilisez la commande suivante :
    ```bash
    php bin/phpunit
    ```

2. Pour mettre à jour le schéma de la base de données de test avant d'exécuter les tests, exécutez :
    - **Mettez à jour le schéma de la base de données de test** (assurez-vous que les paramètres de la base de données de test sont configurés dans le fichier `.env.test` ou un autre fichier de configuration approprié) :
      ```bash
      php bin/console doctrine:schema:update --env=test --force
      ```

### Documentation du Contrôleur Contact

Le contrôleur `ContactController` gère les opérations CRUD sur les contacts via les API suivantes :

- **GET /contacts** : Récupère la liste de tous les contacts.
- **POST /contacts/new** : Ajoute un nouveau contact.
- **PUT /contacts/{id}/edit** : Met à jour un contact existant.
- **DELETE /contacts/{id}** : Supprime un contact existant.

Le contrôleur utilise les entités `Contact` et fournit des réponses JSON pour chaque opération. Les erreurs, telles que les violations de contrainte unique ou les erreurs de mise à jour, sont renvoyées avec des messages appropriés.

### Exemple de Test Unitaire

Le test unitaire `ContactControllerTest` vérifie les opérations suivantes :

1. **Ajouter un contact** : Envoie une requête POST pour ajouter un contact et vérifie que la réponse est correcte.
2. **Mettre à jour un contact** : Envoie une requête PUT pour mettre à jour un contact existant et vérifie que les données sont correctement mises à jour.
3. **Supprimer un contact** : Envoie une requête DELETE pour supprimer un contact et vérifie que le contact a été effectivement supprimé de la base de données.
