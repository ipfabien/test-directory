## Installation

Ce projet est un micro service REST Symfony 5.4 (PHP 7.4) utilisant Docker, PostgreSQL, Doctrine DBAL et JWT.
Voici les étapes pour le faire tourner localement :

1. **Prérequis**
   - Docker et Docker Compose installés
   - `git` installé

2. **Cloner le dépôt**

3. **Préparer le fichier d'environnement**

Un fichier `.env.exemple` est fourni et versionné pour simplifier l'onboarding :

```bash
cp .env.exemple .env 
```

Les variables importantes déjà renseignées :

- `APP_ENV=dev`
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` pour PostgreSQL Docker
- `API_BASE_URL=http://localhost:8000`
- `JWT_SECRET=...` et `API_TOKEN=...` (token de démo pour les appels API via `make`)

4. **Construire et démarrer les conteneurs**

```bash
make build
make up
```

5. **Installer les dépendances PHP**

Tout se fait dans le conteneur `app` via le `Makefile` :

```bash
make install
```

6. **Appliquer les migrations de base de données**

```bash
make migrate
```

7. **Lancer le serveur HTTP (PHP built-in)**

Dans un terminal séparé :

```bash
make serve
```

L’API sera alors disponible sur `http://localhost:8000`.

8. **Lancer le worker Messenger pour le traitement asynchrone**

Les événements (par exemple l’envoi d’email après la création d’un contact) sont traités par un worker Messenger.  
Dans un autre terminal, lancez :

```bash
make messenger-worker
```

Le worker consommera les messages en file d’attente sur le transport `async_events`.

9. **Ouvrir l’interface graphique des emails (Mailhog)**

Les emails envoyés par l’application en environnement de développement sont capturés par Mailhog.  
Pour ouvrir l’interface web de Mailhog dans votre navigateur :

```bash
make mailhog-ui
```

L’interface sera accessible sur `http://localhost:18025` et vous permettra de visualiser tous les emails envoyés par le micro service.

10. **Appeler l’API avec les commandes `make`**

Quelques exemples :

- Créer un contact :

Phone et note sont optionnels, Email est unique.

```bash
make call-create-contact \
  FIRSTNAME="John" \
  LASTNAME="Doe" \
  EMAIL="john.doe@example.com" \
  PHONE="+33123456789" \
  NOTE="Note de test" \
  MANAGER_ID="11111111-1111-1111-1111-111111111111"
```

- Récupérer un contact par son `externalId` :

```bash
make call-get-contact CONTACT_ID="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
```

- Récupérer le manager d’un contact :

```bash
make call-get-contact-manager CONTACT_ID="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
```

- Lister les contacts avec filtre et pagination :

Tout les paraètres sont optionnels.

```bash
make call-get-contact-list FIRSTNAME= LASTNAME= EMAIL= PHONE= PAGE=1 PER_PAGE=10
```

- Lister les managers (2 managers ont été injectés en migration):

```bash
make call-get-manager-list
```

- Récupérer un manager et ses contacts :

```bash
make call-get-manager MANAGER_ID="11111111-1111-1111-1111-111111111111"
```

Les commandes `make` ajoutent automatiquement le header `Authorization: Bearer ...` en lisant `API_TOKEN` depuis le `.env`, vous n’avez donc aucune configuration supplémentaire à faire pour le JWT en local.

## Qualité et tests

Pour vérifier rapidement la qualité du code et exécuter les tests en local :

- **Analyse statique (phpstan)** :

```bash
make phpstan
```

- **Tests unitaires et fonctionnels (phpunit)** :

```bash
make phpunit
```

- **Formatage du code (php-cs-fixer)** :

```bash
make cs-fix
```

La CI GitHub exécute automatiquement ces trois étapes (php-cs-fixer en mode check, phpstan et phpunit) à chaque push / pull request.

## Exercice

Petit micro service d'une api REST pour gérer les contacts.

Pour l'exercice on fait quelque chose de simple, une entité Contact qui contient quelques informations et une entité manager afin de montrer qu'on ne charge pas toutes les données mais seulement ce qu'on a besoin.

J'ai opté pour un symfony 5.4 sous php 7.4 sans doctrine orm et sans aucun orm.

Niveau architecture c'est plutôt du classique avec un léger DDD.

Concrètement, cela veut dire que la logique métier est isolée dans des objets métiers (Value Objects) et des interfaces de repository, sans entrer dans une architecture hexagonale complète.

Un resolver récupère la request afin de pouvoir la récupérer automatiquement sous forme de DTO dans les endpoint. C'est à dire qu'on enlève la responsabilité de traiter la Request au controller.

J'ai également adopter le concept de port/adapter afin d'avoir un contrat d'interface pour nos repository et l'implémentation que l'on veut (Doctrine DBAL dans notre cas mais on pourrait switcher sur Doctrine ORM ou tout autre ORM en modifiant seulement la config).

Un subscriber s'occupe de gérer les erreurs.

Le concept de SOLID est plutôt bien respecté avec des responsabilités plutôt bien mises en place.

Le concept YAGNI est aussi bien respecté en faisant pas de over engineering mais tout en gardant un micro service propre et scalable.

Des tests ont été ajoutés ainsi que php-cs-fixer, phpstan level 7, une CI avec 3 jobs.

Tout est bien sur discutable mais chaque choix est justifiable.

## Ajout de Messenger

Ajout de messenger afin de montrer son utilisation dans un cas concret. Il est principalement utiliser pour de l'asynchrone donc l'idée va être de créer un évènement lorsque l'on a créé un contact et qu'en le consommant de façon asynchrone (via doctrine dans notre cas pour cet usage simple) on va envoyer un email à l'administrateur.

L'event est dispatch dans l'endpoint pour rester dans un cas d'architecture assez simple mais en réalité ce n'est pas sa responsabilité et on devrait le dispatch ailleurs.

Pour tester si l'email a bien été envoyé j'ai listé plus haut une commande make pour lancer le worker et une commande make pour ouvrir l'interface graphique de MailHog afin de consulter les emails.
