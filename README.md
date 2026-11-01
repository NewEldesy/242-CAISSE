# 242-CAISSE

Système de gestion multi-boutiques **offline-first** basé sur une architecture Domain-Driven Design (DDD).

Chaque boutique fonctionne de manière totalement autonome avec une base SQLite locale. Un serveur central Laravel est prévu pour la synchronisation et la supervision de l'ensemble du réseau.

---

## Architecture

```
                        SERVEUR CENTRAL
                        Laravel + API REST
                               │
                        Contrat de Sync
                               │
              ┌────────────────┼────────────────┐
              │                │                │
              ▼                ▼                ▼
         Boutique A       Boutique B       Boutique C
           SQLite           SQLite           SQLite
           CLI POS          CLI POS          CLI POS
```

---

## Structure du projet

Monorepo PHP organisé en 4 packages indépendants :

| Package | Rôle |
|---------|------|
| `caisse/domain` | Entités, value objects, événements, interfaces des dépôts |
| `caisse/infrastructure` | Implémentations SQLite, migrations, connexion PDO |
| `caisse/cli` | Application en ligne de commande (POS) |
| `caisse/sync` | Moteur de synchronisation vers le serveur central |

---

## Prérequis

- PHP 8.0+
- Composer
- Extensions PHP : `pdo`, `pdo_sqlite`

---

## Installation

```bash
git clone <repo>
cd 242-CAISSE
composer install
cp .env.example .env
```

---

## Initialisation d'une boutique

```bash
php packages/cli/bin/pos init
```

Crée le fichier `database/caisse.db` et applique toutes les migrations.

---

## Commandes disponibles

```bash
# Boutique et utilisateurs
php packages/cli/bin/pos store:create <nom> <code>
php packages/cli/bin/pos user:create <store_code> <username> <password> <role>
php packages/cli/bin/pos device:create <store_code> <name>
php packages/cli/bin/pos device:list <store_code>

# Catalogue
php packages/cli/bin/pos product:create <store_code> <name> <price> [category]
php packages/cli/bin/pos product:list <store_code>
php packages/cli/bin/pos stock:list <store_code>

# Ventes
php packages/cli/bin/pos session:open <store_code> <device_id> <user_id>
php packages/cli/bin/pos sale:create <store_code> <device_id> <user_id> [customer_id] [product_id:qty ...]
php packages/cli/bin/pos sale:list <store_code>
php packages/cli/bin/pos session:close <session_id>
```

---

## Tests

```bash
composer test
# ou directement
vendor/bin/phpunit --no-coverage
```

---

## Configuration

Copier `.env.example` en `.env` et renseigner les valeurs selon l'environnement :

| Variable | Description | Défaut |
|----------|-------------|--------|
| `APP_ENV` | Environnement (`local` / `production`) | `local` |
| `DB_PATH` | Chemin vers le fichier SQLite | `database/caisse.db` |
| `SYNC_API_URL` | URL du serveur central (Phase 4) | / |
| `SYNC_API_KEY` | Clé d'API pour la synchronisation (Phase 4) | / |

---

## Roadmap

- [x] Phase 0 : Architecture de base (domaine, infrastructure, CLI)
- [ ] Phase 1 : POS local complet (ventes, paiements, stock)
- [ ] Phase 2 : Sessions de caisse et rapports
- [ ] Phase 3 : Gestion multi-utilisateurs
- [ ] Phase 4 : Moteur de synchronisation local → serveur central
- [ ] Phase 5 : API REST Laravel (serveur central)
- [ ] Phase 6 : Dashboard web multi-boutiques
- [ ] Phase 7 : Synchronisation bidirectionnelle

---

## Auteur

[Ramel Offrey Eldesy MAKOSSO](https://github.com/NewEldesy)