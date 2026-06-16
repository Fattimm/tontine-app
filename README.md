# TontineApp

Application web de gestion de tontines — projet pédagogique Laravel 13 (PHP 8.3).

---

## Stack

PHP 8.3 · Laravel 13 · MySQL · Blade + Bootstrap 5 · DomPDF

---

## Installation

```bash
git clone <repo> tontine-app && cd tontine-app
composer install
cp .env.example .env && php artisan key:generate
```

Configurer `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD), puis :

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

---

## Comptes par défaut

| Email | Mot de passe | Rôle |
|---|---|---|
| admin@tontine.sn | passer123 | Admin |
| organisateur1@tontine.sn | passer123 | Organisateur |
| organisateur2@tontine.sn | passer123 | Organisateur |

Les membres reçoivent un lien de création de mot de passe à la création de leur compte.

---

## Rôles

| Rôle | Accès |
|---|---|
| `admin` | Gestion des comptes utilisateurs, restauration corbeille |
| `organisateur` | CRUD membres, tontines, cotisations, tours (ses ressources uniquement) |
| `membre` | Dashboard personnel, enregistrement de ses cotisations |

---

## Fonctionnalités

- Gestion des tontines (CRUD, statuts, tirage aléatoire)
- Gestion des membres avec compte utilisateur lié
- Cotisations avec détection de doublon et gestion des réserves
- Validation des cotisations par l'organisateur (membre soumet → organisateur valide)
- Notifications email (confirmation cotisation après validation, tirage au sort)
- Export PDF des cotisations et récapitulatif tontine
- Mot de passe oublié / réinitialisation
- Soft deletes avec corbeille sur toutes les entités
- Policies par rôle sur toutes les ressources
- 25 tests Feature (auth, rôles, cotisations, policies)

---

## Architecture

```
Controller → Service → Model
```

Chaque contrôleur délègue la logique métier à son service dédié :
`TontineService`, `CotisationService`, `MembreService`, `TourService`, `UserService`

---

## Configuration mail

Développement : `MAIL_MAILER=log` — emails écrits dans `storage/logs/laravel.log`

Production (Gmail) :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=ton.email@gmail.com
MAIL_PASSWORD="mot de passe application Google"
```

---

## Tests

```bash
# Créer la base de test (une seule fois)
mysql -u root -p -e "CREATE DATABASE tontine_test;"

# Lancer les tests
php artisan test
```

---

## Commandes utiles

```bash
php artisan migrate:status
php artisan route:list
php artisan test
php artisan tinker
```
