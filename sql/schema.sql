-- ============================================
-- Starter Kit Database Schema
-- Database: PostgreSQL
-- ============================================

-- Create schemas
CREATE SCHEMA IF NOT EXISTS starter_app;
CREATE SCHEMA IF NOT EXISTS so;

-- ============================================
-- SSO Schema (so) - User accounts
-- ============================================

CREATE TABLE IF NOT EXISTS so.akn_user (
    userid SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(255),
    email VARCHAR(255),
    status SMALLINT DEFAULT 1,
    auth_key VARCHAR(255),
    id_pegawai INTEGER,
    roles VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

-- Insert default admin user (password: admin123 - md5)
INSERT INTO so.akn_user (username, password, nama, email, status, auth_key, roles)
VALUES ('admin', md5('admin123'), 'Administrator', 'admin@starter.aa', 1, 'admin-auth-key', 'admin')
ON CONFLICT (username) DO NOTHING;

-- Insert sample users
INSERT INTO so.akn_user (username, password, nama, email, status, auth_key, roles)
VALUES 
    ('user1', md5('user123'), 'User Satu', 'user1@starter.aa', 1, 'user1-auth-key', 'staff'),
    ('user2', md5('user123'), 'User Dua', 'user2@starter.aa', 1, 'user2-auth-key', 'staff')
ON CONFLICT (username) DO NOTHING;

-- ============================================
-- Starter App Schema - RBAC Tables
-- ============================================

CREATE TABLE IF NOT EXISTS starter_app.auth_rule (
    name VARCHAR(64) NOT NULL PRIMARY KEY,
    data BYTEA,
    created_at INTEGER,
    updated_at INTEGER
);

CREATE TABLE IF NOT EXISTS starter_app.auth_item (
    name VARCHAR(64) NOT NULL PRIMARY KEY,
    type SMALLINT NOT NULL,
    description TEXT,
    rule_name VARCHAR(64),
    data BYTEA,
    created_at INTEGER,
    updated_at INTEGER,
    CONSTRAINT auth_item_rule_name_fk FOREIGN KEY (rule_name) REFERENCES starter_app.auth_rule (name) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_auth_item_type ON starter_app.auth_item (type);

CREATE TABLE IF NOT EXISTS starter_app.auth_item_child (
    parent VARCHAR(64) NOT NULL,
    child VARCHAR(64) NOT NULL,
    PRIMARY KEY (parent, child),
    CONSTRAINT auth_item_child_parent_fk FOREIGN KEY (parent) REFERENCES starter_app.auth_item (name) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT auth_item_child_child_fk FOREIGN KEY (child) REFERENCES starter_app.auth_item (name) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS starter_app.auth_assignment (
    item_name VARCHAR(64) NOT NULL,
    user_id VARCHAR(64) NOT NULL,
    created_at INTEGER,
    PRIMARY KEY (item_name, user_id),
    CONSTRAINT auth_assignment_item_name_fk FOREIGN KEY (item_name) REFERENCES starter_app.auth_item (name) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Menu table for RBAC
CREATE TABLE IF NOT EXISTS starter_app.menu (
    id SERIAL PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    parent INTEGER,
    route VARCHAR(255),
    "order" INTEGER,
    data TEXT,
    CONSTRAINT menu_parent_fk FOREIGN KEY (parent) REFERENCES starter_app.menu (id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- ============================================
-- Starter App Schema - Example Tables
-- ============================================

CREATE TABLE IF NOT EXISTS starter_app.tb_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

-- Insert sample categories
INSERT INTO starter_app.tb_categories (name, description, is_active) VALUES
    ('Electronics', 'Electronic devices and gadgets', true),
    ('Books', 'Physical and digital books', true),
    ('Clothing', 'Apparel and fashion items', true),
    ('Food & Beverages', 'Food and drink products', true),
    ('Sports', 'Sports equipment and accessories', false)
ON CONFLICT DO NOTHING;

-- ============================================
-- Insert default RBAC data
-- ============================================

-- Insert roles (type = 1)
INSERT INTO starter_app.auth_item (name, type, description, created_at, updated_at) VALUES
    ('admin', 1, 'Administrator role', EXTRACT(EPOCH FROM NOW())::INTEGER, EXTRACT(EPOCH FROM NOW())::INTEGER),
    ('staff', 1, 'Staff role', EXTRACT(EPOCH FROM NOW())::INTEGER, EXTRACT(EPOCH FROM NOW())::INTEGER)
ON CONFLICT (name) DO NOTHING;

-- Insert permissions (type = 2)
INSERT INTO starter_app.auth_item (name, type, description, created_at, updated_at) VALUES
    ('dashboard', 2, 'Access dashboard', EXTRACT(EPOCH FROM NOW())::INTEGER, EXTRACT(EPOCH FROM NOW())::INTEGER),
    ('categories', 2, 'Manage categories', EXTRACT(EPOCH FROM NOW())::INTEGER, EXTRACT(EPOCH FROM NOW())::INTEGER),
    ('rbac', 2, 'Manage RBAC', EXTRACT(EPOCH FROM NOW())::INTEGER, EXTRACT(EPOCH FROM NOW())::INTEGER),
    ('root', 2, 'Root access', EXTRACT(EPOCH FROM NOW())::INTEGER, EXTRACT(EPOCH FROM NOW())::INTEGER)
ON CONFLICT (name) DO NOTHING;

-- Assign permissions to admin role
INSERT INTO starter_app.auth_item_child (parent, child) VALUES
    ('admin', 'dashboard'),
    ('admin', 'categories'),
    ('admin', 'rbac'),
    ('admin', 'root')
ON CONFLICT DO NOTHING;

-- Assign permissions to staff role
INSERT INTO starter_app.auth_item_child (parent, child) VALUES
    ('staff', 'dashboard'),
    ('staff', 'categories')
ON CONFLICT DO NOTHING;

-- Assign admin role to admin user (userid = 1)
INSERT INTO starter_app.auth_assignment (item_name, user_id, created_at) VALUES
    ('admin', '1', EXTRACT(EPOCH FROM NOW())::INTEGER)
ON CONFLICT DO NOTHING;

-- Assign staff role to user1 (userid = 2)
INSERT INTO starter_app.auth_assignment (item_name, user_id, created_at) VALUES
    ('staff', '2', EXTRACT(EPOCH FROM NOW())::INTEGER)
ON CONFLICT DO NOTHING;
