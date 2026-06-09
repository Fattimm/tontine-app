# TontineApp

Application web de gestion de tontines — projet pédagogique Laravel 13 (PHP 8.3).

---

## Stack

PHP 8.3 · Laravel 13 · MySQL · Blade + Bootstrap 5

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

Les membres reçoivent un lien de création de mot de passe (valable 7 jours) à la création de leur compte.

---

## Rôles

| Rôle | Accès |
|---|---|
| `admin` | Gestion des comptes, restauration corbeille |
| `organisateur` | CRUD membres, tontines, cotisations, tours (ses ressources uniquement) |
| `membre` | Dashboard personnel, enregistrement de ses cotisations |

---

## Commandes utiles

```bash
php artisan migrate:status
php artisan route:list
php artisan tinker
```
