# 🎓 Plateforme de Recherche de Stage - Web4All

## 📖 À propos

Ce projet a été réalisé dans le cadre du bloc "Développement Web" du cursus CESI. L'objectif est de développer une solution web complète facilitant la mise en relation entre les étudiants en recherche de stage et les entreprises.

L'application, développée par l'équipe **Web4All**, permet de centraliser les offres de stage, de gérer les candidatures et d'administrer les comptes étudiants et pilotes de promotion.

> **Note :** Ce projet est réalisé en **PHP Natif** (sans framework type Symfony/Laravel) avec une architecture **MVC** construite sur mesure, conformément au cahier des charges.

## ✨ Fonctionnalités Principales

### 👨‍🎓 Espace Étudiant

* Consultation des offres de stage avec filtres de recherche.
* Ajout d'offres en **Wish-list**.
* Système de candidature (upload CV + Lettre de motivation).
* Suivi des candidatures ("Postulée", "En attente", etc.).
* Tableau de bord statistique personnel.

### 🏢 Gestion des Entreprises & Offres

* CRUD complet des entreprises (Création, Lecture, Mise à jour, Suppression).
* Évaluation des entreprises par les pilotes/étudiants.
* Publication et gestion des offres de stage (Compétences, Rémunération, Dates).

### 👨‍🏫 Espace Administration (Pilotes)

* Gestion des comptes (Étudiants, Pilotes).
* Modération des offres et des entreprises.
* Accès aux statistiques globales (Offres par durée, top wish-list, etc.).

### 📱 Transverse & Technique

* **Responsive Design** (Approche Mobile First).
* **PWA** (Progressive Web App) : Installable sur mobile.
* **Sécurité** : Protection CSRF, XSS, Hachage des mots de passe.
* **SEO** : Url rewriting, Sitemap, Optimisation des balises meta.

## 🛠️ Stack Technique

Le projet respecte les contraintes strictes suivantes :

* **Backend :** PHP 8+ (Orienté Objet), Architecture MVC personnalisée, Moteur de template maison.
* **Frontend :** HTML5, CSS3 (Sass/SCSS possible), JavaScript (Vanilla / jQuery).
* **Base de données :** MySQL / MariaDB.
* **Serveur :** Apache (Configuration VHost requise).
* **Tests :** PHPUnit.

## 🚀 Installation et Démarrage

### Prérequis

* Un serveur local (XAMPP, WAMP, MAMP ou Docker).
* PHP 8.0 ou supérieur.
* Composer (pour PHPUnit uniquement).

### Étapes d'installation

1. **Cloner le dépôt**
```bash
git clone https://github.com/[votre-pseudo]/[nom-du-repo].git

```


2. **Base de données**
* Créez une base de données nommée `stage_search_db` (ou autre).
* Importez le fichier SQL situé dans `/database/init.sql`.
* Configurez les accès dans le fichier `/config/database.php` (ou `.env` si implémenté) :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_db');
define('DB_USER', 'root');
define('DB_PASS', '');

```




3. **Configuration du VHost (Apache)**
Le projet nécessite un VHost pour séparer les assets statiques et le routage. Ajoutez ceci à votre configuration Apache :
```apache
<VirtualHost *:80>
    ServerName projet-stage.local
    DocumentRoot "C:/chemin/vers/repo/public"
    <Directory "C:/chemin/vers/repo/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

```


*(N'oubliez pas d'ajouter `127.0.0.1 projet-stage.local` dans votre fichier hosts).*
4. **Lancer le projet**
Accédez à `http://projet-stage.local` dans votre navigateur.

## 📂 Structure du Projet (MVC)

```
/root
├── /assets          # CSS, JS, Images (Dossier public via VHost)
├── /config          # Configuration DB et Globales
├── /controllers     # Logique de contrôle
├── /models          # Accès aux données (DAO)
├── /views           # Fichiers de template (HTML/PHP)
├── /core            # Routeur, Classe de base Controller/Model
├── /tests           # Tests unitaires PHPUnit
└── /public          # Point d'entrée (index.php)

```

## 👥 L'équipe Web4All

* **Tom Romanin** - *Scrum Master / Backend Lead*
* **Mathéo Goaoc** - *Product Owner / Frontend Lead*
* **Émilien Rousseau** - *Développeur Fullstack* 
* **Charles Devines** - *Développeur Fullstack / DB Architect*

## 📝 Licence & Droits

Projet réalisé dans un cadre pédagogique pour CESI École d'Ingénieurs.
© 2026 Web4All.
