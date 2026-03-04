Cette application web permet de répertorier les trajets effectués par les employées de l'entreprise. 
Ainsi ils peuvent voir les trajets disponibles, en créer ou modifier ceux dont ils sont l'auteur.

L’application est construite avec le modèle MVC en PHP, avec Bootstrap pour le front-end.

Prérequis
Serveur local type XAMPP, MAMP ou WAMP
PHP 8+
MySQL
Navigateur moderne (Chrome, Firefox, Edge…)

Installation
Cloner le projet
git clone <URL_DU_REPO>
Importer la base de données
Ouvrir phpMyAdmin ou un outil MySQL
Importer le fichier SQL fourni database.sql
Configurer la connexion à la base
Modifier le fichier config/db.php avec vos identifiants MySQL :

$host = 'localhost';
$db   = 'nom_de_la_bdd';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

Démarrer le serveur
Pour XAMPP : lancer Apache et MySQL
Accéder à l’application via http://localhost/nom_du_projet/public/index.php

Utilisation
Connexion
Utiliser les identifiants fournis pour les différents rôles :
Admin : accès complet
Employé : accès limité à ses trajets

Dashboard Employé
Voir ses trajets
Créer ou modifier ses trajets
Voir autres trajets disponibles avec infos auteur

Dashboard Admin
Voir tous les trajets et en supprimer
Gérer les agences (création, modification, suppression)
Voir les utilisateurs

Structure du projet
app/
  controllers/    # Contrôleurs (User, Trajet, Admin)
  models/         # Modèles (User, Trajet, Agence)
  services/       # Logique métier et interaction base de données
  views/          # Pages (home, dashboard, admin-dashboard, formulaires)
config/
  db.php          # Connexion à la base
public/
  index.php       # Point d’entrée de l’application
vendor/           # Dépendances (Composer)

Technologies utilisées
PHP 8+ (Backend MVC)
MySQL (Base de données)
Bootstrap 5 (Interface responsive)
Composer (Autoloading et dépendances)
