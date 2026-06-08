# Travaux Orange – Affichage des interventions

Application web interne permettant d’afficher sur un écran de bureau ou une TV les travaux réseau Orange prévus pour la journée, avec une interface d’administration pour planifier les messages.

![admin](1.png)
![user](2.png)

## Fonctionnalités

- Affichage des interventions prévues pour la date du jour.
- Interface utilisateur optimisée pour un affichage sur TV.
- Interface d’administration pour créer, modifier et supprimer des messages.
- Saisie d’une date d’affichage obligatoire, d’un titre optionnel et d’une description détaillée.
- Mode clair / mode sombre avec bascule dans l’interface.
- Stockage local dans SQLite.
- Déploiement simplifié avec Docker.

## Stack technique

- **Backend** : PHP 8.x
- **Base de données** : SQLite
- **Frontend** : HTML, CSS, JavaScript vanilla
- **Déploiement** : Docker + Docker Compose

## Architecture du projet

```text
travaux-orange-pro/
├── admin.php
├── user.php
├── db.php
├── cleanup.php
├── Dockerfile
├── docker-compose.yml
├── css/
│   ├── base.css
│   ├── user.css
│   └── admin.css
├── js/
│   └── theme.js
└── data/
    └── travaux.db
```

## Base de données

La table `messages` contient les champs suivants :

- `id` : clé primaire auto-incrémentée
- `display_date` : date d’affichage au format `YYYY-MM-DD`
- `title` : titre optionnel
- `body` : description du message
- `created_at` : date de création

La base SQLite est créée automatiquement au premier lancement.

## Déploiement avec Docker

### Prérequis

- Docker
- Docker Compose

### Dockerfile


### docker-compose.yml


### Lancement

Depuis la racine du projet :

```bash
docker compose up -d --build
```

L’application sera accessible sur :

- `http://localhost:8080/user.php`
- `http://localhost:8080/admin.php`

Pour arrêter les conteneurs :

```bash
docker compose down
```

## Déploiement sur un serveur Linux

### 1. Cloner le projet

```bash
git clone https://github.com/guillaumemen/travaux-orange-pro-V2.git
cd travaux-orange-pro
```

### 2. Démarrer l’application

```bash
docker compose up -d --build
```

### 3. Vérifier l’état

```bash
docker compose ps
docker compose logs -f
```

## Mise à jour

Après modification du projet :

```bash
git pull
docker compose up -d --build
```

## Persistance des données

Le fichier SQLite est stocké dans le dossier `data/`.

Le volume Docker suivant permet de conserver les données même après reconstruction du conteneur :

```yaml
- ./data:/app/data
```

## Utilisation

### Interface admin (`admin.php`)

Permet de :

- créer un message de travaux
- modifier un message existant
- supprimer un message
- planifier un affichage pour une date précise

Chaque message contient :

- une date d’affichage
- un titre optionnel
- une description

### Interface utilisateur (`user.php`)

Permet d’afficher uniquement les messages prévus pour la journée en cours.

Cette vue est pensée pour un affichage sur écran ou TV avec une présentation lisible et épurée.

## Personnalisation

- Les couleurs et variables globales sont définies dans `css/base.css`.
- Les styles de la vue TV sont dans `css/user.css`.
- Les styles de l’interface d’administration sont dans `css/admin.css`.
- Le comportement du thème est géré dans `js/theme.js`.

## Évolutions possibles

- Ajouter un second affichage pour d’autres services internes.
