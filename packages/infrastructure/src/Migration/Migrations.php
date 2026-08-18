<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Migration;

return [
    "CREATE TABLE IF NOT EXISTS stores (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        code TEXT NOT NULL UNIQUE,
        status TEXT NOT NULL DEFAULT 'active',
        address TEXT,
        phone TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS users (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        username TEXT NOT NULL,
        email TEXT NOT NULL,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'active',
        full_name TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id)
    )",

    "CREATE TABLE IF NOT EXISTS devices (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        name TEXT NOT NULL,
        identifier TEXT NOT NULL UNIQUE,
        type TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id)
    )",

    "CREATE TABLE IF NOT EXISTS categories (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        description TEXT,
        parent_id TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (parent_id) REFERENCES categories(id)
    )",

    "CREATE TABLE IF NOT EXISTS products (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        category_id TEXT,
        name TEXT NOT NULL,
        description TEXT,
        reference TEXT UNIQUE,
        selling_price INTEGER NOT NULL,
        purchase_price INTEGER NOT NULL,
        currency TEXT NOT NULL DEFAULT 'XAF',
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id),
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )",

    "CREATE TABLE IF NOT EXISTS customers (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        first_name TEXT,
        last_name TEXT,
        phone TEXT,
        email TEXT,
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id)
    )",

    "CREATE TABLE IF NOT EXISTS sales (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        device_id TEXT NOT NULL,
        user_id TEXT NOT NULL,
        customer_id TEXT,
        cash_session_id TEXT,
        status TEXT NOT NULL DEFAULT 'pending',
        total_amount INTEGER NOT NULL,
        currency TEXT NOT NULL DEFAULT 'XAF',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id),
        FOREIGN KEY (device_id) REFERENCES devices(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (cash_session_id) REFERENCES cash_sessions(id)
    )",

    "CREATE TABLE IF NOT EXISTS sale_items (
        id TEXT PRIMARY KEY,
        sale_id TEXT NOT NULL,
        product_id TEXT NOT NULL,
        product_name TEXT NOT NULL,
        product_reference TEXT,
        quantity INTEGER NOT NULL,
        unit_price INTEGER NOT NULL,
        currency TEXT NOT NULL DEFAULT 'XAF',
        subtotal INTEGER NOT NULL,
        created_at TEXT NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS payments (
        id TEXT PRIMARY KEY,
        sale_id TEXT NOT NULL,
        method TEXT NOT NULL,
        amount INTEGER NOT NULL,
        reference TEXT,
        created_at TEXT NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS stock (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        product_id TEXT NOT NULL UNIQUE,
        quantity INTEGER NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'available',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id)
    )",

    "CREATE TABLE IF NOT EXISTS stock_movements (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        product_id TEXT NOT NULL,
        sale_id TEXT,
        type TEXT NOT NULL,
        quantity_before INTEGER NOT NULL,
        quantity_after INTEGER NOT NULL,
        notes TEXT,
        created_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id)
    )",

    "CREATE TABLE IF NOT EXISTS cash_sessions (
        id TEXT PRIMARY KEY,
        store_id TEXT NOT NULL,
        device_id TEXT NOT NULL,
        user_id TEXT NOT NULL,
        opening_amount INTEGER NOT NULL,
        closing_amount INTEGER,
        notes TEXT,
        closed_at TEXT,
        created_at TEXT NOT NULL,
        FOREIGN KEY (store_id) REFERENCES stores(id),
        FOREIGN KEY (device_id) REFERENCES devices(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )",

    "CREATE TABLE IF NOT EXISTS sync_queue (
        id TEXT PRIMARY KEY,
        event_id TEXT NOT NULL UNIQUE,
        aggregate_type TEXT NOT NULL,
        aggregate_id TEXT NOT NULL,
        event_name TEXT NOT NULL,
        payload TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'pending',
        retry_count INTEGER NOT NULL DEFAULT 0,
        last_error TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS sync_logs (
        id TEXT PRIMARY KEY,
        event_id TEXT NOT NULL,
        status TEXT NOT NULL,
        http_status INTEGER,
        response_body TEXT,
        error_message TEXT,
        executed_at TEXT NOT NULL
    )",

    "CREATE INDEX IF NOT EXISTS idx_users_store_id ON users(store_id)",
    "CREATE INDEX IF NOT EXISTS idx_products_store_id ON products(store_id)",
    "CREATE INDEX IF NOT EXISTS idx_products_category_id ON products(category_id)",
    "CREATE INDEX IF NOT EXISTS idx_sales_store_id ON sales(store_id)",
    "CREATE INDEX IF NOT EXISTS idx_sales_cash_session_id ON sales(cash_session_id)",
    "CREATE INDEX IF NOT EXISTS idx_sale_items_sale_id ON sale_items(sale_id)",
    "CREATE INDEX IF NOT EXISTS idx_payments_sale_id ON payments(sale_id)",
    "CREATE INDEX IF NOT EXISTS idx_stock_store_product ON stock(store_id, product_id)",
    "CREATE INDEX IF NOT EXISTS idx_stock_movements_store_id ON stock_movements(store_id)",
    "CREATE INDEX IF NOT EXISTS idx_cash_sessions_store_id ON cash_sessions(store_id)",
    "CREATE INDEX IF NOT EXISTS idx_sync_queue_status ON sync_queue(status)",
    "CREATE INDEX IF NOT EXISTS idx_sync_logs_event_id ON sync_logs(event_id)",
];
