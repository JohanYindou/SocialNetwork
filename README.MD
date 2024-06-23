# SocialNetwork

![SocialNetwork](/uploads/medias/Logo_SocialNetwork.png)

## Table des matières

  - [Description](#description)
  - [Prérequis](#prérequis)
  - [Installation](#installation)
  - [Démarrage](#démarrage)
  - [Architecture](#architecture)
  - [Fonctionnalités](#fonctionnalités)
  - [Auteur](#auteur)
  - [Contribution](#contribution)

## Description

SocialNetwork est un réseau social d'entreprise qui permet aux employés de partager des informations et de communiquer avec leurs collègues. Cette plateforme inclut des fonctionnalités telles que l'authentification des utilisateurs, la publication de contenu, la gestion des commentaires, les notifications, la messagerie privée et bien plus encore.

## Prérequis

- PHP 8.0 ou supérieur
- Composer
- Symfony 7
- MySQL ou un autre SGBD compatible

## Installation

Pour installer SocialNetwork, suivez ces étapes :
Clonez le dépôt sur votre machine locale :

```bash
git clone https://github.com/JohanYindou/SocialNetwork.git
cd SocialNetwork
```

Installez les dépendances avec Composer :

```bash
composer install
```

Configurez la base de données en modifiant le fichier .env ou en créant un fichier .env.local avec la variable DATABASE_URL.

```bash
symfony console doctrine:database:create
```

La variable MailerDSN qui est nécessaire à l'application.
Vous pourez vous en créer un sur [Mailtrap](mailtrap.io).
et l'ajouter dans votre fichier .env ou .env.local.

Ensuite, pour créer les migrations :

```bash
symfony console make:migration
```

Pour appliquer les migrations à la base de données :

```bash
symfony console doctrine:migrations:migrate
```

Enfin, il faut charger les fixtures générées par Faker dans la base de données :

```bash
symfony console doctrine:fixtures:load
```

## Démarrage

Pour démarrer l'application, utilisez la commande suivante :

```bash
symfony serve
```

## Architecture

SocialNetwork est développé en Symfony 7 et suit le pattern MVC (Model-View-Controller).

## Fonctionnalités

Les fonctionalitées pricipales de cette application sont :

- Authentification et gestion des utilisateurs
- Publication de contenu
- Gestion des commentaires
- Système de notifications
- Recherche et filtrage
- Messagerie privée

## Auteur

Ce projet a été réalisé par :

Johan Yindou - [GitHub](https://github.com/JohanYindou)

## Contribution

Les contributions sont les bienvenues ! Veuillez soumettre une pull request ou ouvrir une issue pour toute suggestion ou problème.
