# Multi-Store Management System

Application de gestion multi-boutiques conçue selon une architecture
offline-first.

Le système permet à chaque boutique de fonctionner de manière autonome
avec une base de données locale SQLite, tout en prévoyant une
synchronisation avec un serveur central.

## Vision

Le système est composé de deux niveaux :

1. Une application locale installée dans chaque boutique.
2. Un serveur central Laravel permettant la synchronisation et la
   supervision de l'ensemble des boutiques.

Le dashboard central sera développé après stabilisation de la caisse
locale.

Cependant, le contrat de synchronisation est défini dès le début du
projet et constitue une contrainte d'architecture fondamentale.

## Architecture

'''
                         CENTRAL SERVER
                        Laravel + API
                              │
                              │
                       Sync Contract
                              │
             ┌────────────────┼────────────────┐
             │                │                │
             ▼                ▼                ▼
            Boutique A       Boutique B       Boutique C
            SQLite           SQLite           SQLite
            POS              POS              POS
'''