-- ============================================================
-- 5TH BREW - Database Schema
-- Coffee company e-commerce project (CCS0043 Final Project)
-- Import this whole file via phpMyAdmin on your host.
-- ============================================================

-- NOTE: no CREATE DATABASE / USE here on purpose — free hosts like
-- InfinityFree assign you a database already (e.g. if0_xxxxx_fifthbrew)
-- and phpMyAdmin runs this import inside it directly. If you're running
-- this locally in XAMPP instead, just create/select the database first
-- in phpMyAdmin, then import this file.

-- ---------------------------------------------------------
-- SELLER SIDE
-- ---------------------------------------------------------

CREATE TABLE admins (
    id INT NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'admin',
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (username)
);

CREATE TABLE audit_log (
    id INT NOT NULL AUTO_INCREMENT,
    admin_id INT NOT NULL,
    admin_username VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
);

-- ---------------------------------------------------------
-- PRODUCT CATALOG
-- ---------------------------------------------------------

CREATE TABLE categories (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE products (
    id INT NOT NULL AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    is_special INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
);

-- Each product can come in different sizes / temperatures, each
-- with its own price and its own stock count (this is what the
-- admin "manage stocks" page edits).
CREATE TABLE product_options (
    id INT NOT NULL AUTO_INCREMENT,
    product_id INT NOT NULL,
    size_label VARCHAR(30) NOT NULL,   -- e.g. Lupa (12oz)
    temperature VARCHAR(20) NOT NULL,  -- Hot / Cold / Cold Brew / Morning Brew
    price DECIMAL(8,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

-- ---------------------------------------------------------
-- BUYER SIDE
-- ---------------------------------------------------------

CREATE TABLE buyers (
    id INT NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    is_verified INT NOT NULL DEFAULT 0,
    verify_token VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (email)
);

CREATE TABLE cart_items (
    id INT NOT NULL AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    product_option_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at DATETIME NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE orders (
    id INT NOT NULL AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    order_date DATETIME NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE order_items (
    id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    size_label VARCHAR(30) NOT NULL,
    temperature VARCHAR(20) NOT NULL,
    quantity INT NOT NULL,
    price_each DECIMAL(8,2) NOT NULL,
    PRIMARY KEY (id)
);

-- ---------------------------------------------------------
-- SEED DATA: categories
-- ---------------------------------------------------------
INSERT INTO categories (name) VALUES
('Milk-Based Lattes'),
('Specialty Lattes'),
('Tea & Brews'),
('Special');

-- ---------------------------------------------------------
-- SEED DATA: products
-- ---------------------------------------------------------
INSERT INTO products (category_id, name, description, image, is_special, created_at) VALUES
(1,'Matcha Latte','Premium matcha whisked with steamed milk.',NULL,0,NOW()),
(1,'Matcha Blueberry Latte','Matcha latte with a blueberry twist.','matcha-blueberry-latte.png',0,NOW()),
(1,'Coconut Latte','Espresso with coconut milk.','coconut-latte.png',0,NOW()),
(1,'Ube Latte','Espresso with real ube flavoring.','ube-latte.png',0,NOW()),
(1,'Spanish Latte','Sweet, creamy classic Spanish-style latte.','spanish-latte.png',0,NOW()),
(1,'Cappuccino','Classic espresso with steamed milk foam.','cappuccino.png',0,NOW()),
(2,'5th Brew Latte (Double Shot Breve Latte)','Our signature double-shot breve latte.','5th-brew-latte.png',0,NOW()),
(2,'Tag-Ulan Latte (Honey Kalingag Latte)','Honey and Philippine cinnamon (kalingag), served hot only.','honey-kalingag-latte.png',0,NOW()),
(2,'Tag-Init Latte (Muscovado Vanilla Breve Latte)','Muscovado vanilla breve, served cold only.','muscovado-vanilla-latte.png',0,NOW()),
(3,'Binhi Latte (Roasted Rice Brew)','Roasted rice brew, cold brew or morning brew.','binhi-latte.png',0,NOW()),
(3,'Kalamansi Tea','Refreshing Philippine calamansi tea.','kalamansi-tea.png',0,NOW()),
(3,'Tablea Latte (Davao Cacao Chocolate Latte)','Made from Davao tablea cacao.','tablea-latte.png',0,NOW()),
(4,'Asin Tibuok Latte (Philippine Sea Salt Latte)','SPECIAL: rare Philippine sea salt latte.','asin-tibuok-latte.png',1,NOW());

-- ---------------------------------------------------------
-- SEED DATA: product_options (sizes: Lupa 12oz / Dagat 16oz / Araw 22oz)
-- ---------------------------------------------------------
-- Products 1-6: full size range, Hot or Cold
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(1,'Lupa (12oz)','Hot',120,30),(1,'Lupa (12oz)','Cold',130,30),
(1,'Dagat (16oz)','Hot',140,30),(1,'Dagat (16oz)','Cold',150,30),
(1,'Araw (22oz)','Hot',160,30),(1,'Araw (22oz)','Cold',170,30),

(2,'Lupa (12oz)','Hot',130,30),(2,'Lupa (12oz)','Cold',140,30),
(2,'Dagat (16oz)','Hot',150,30),(2,'Dagat (16oz)','Cold',160,30),
(2,'Araw (22oz)','Hot',170,30),(2,'Araw (22oz)','Cold',180,30),

(3,'Lupa (12oz)','Hot',110,30),(3,'Lupa (12oz)','Cold',120,30),
(3,'Dagat (16oz)','Hot',130,30),(3,'Dagat (16oz)','Cold',140,30),
(3,'Araw (22oz)','Hot',150,30),(3,'Araw (22oz)','Cold',160,30),

(4,'Lupa (12oz)','Hot',130,30),(4,'Lupa (12oz)','Cold',140,30),
(4,'Dagat (16oz)','Hot',150,30),(4,'Dagat (16oz)','Cold',160,30),
(4,'Araw (22oz)','Hot',170,30),(4,'Araw (22oz)','Cold',180,30),

(5,'Lupa (12oz)','Hot',110,30),(5,'Lupa (12oz)','Cold',120,30),
(5,'Dagat (16oz)','Hot',130,30),(5,'Dagat (16oz)','Cold',140,30),
(5,'Araw (22oz)','Hot',150,30),(5,'Araw (22oz)','Cold',160,30),

(6,'Lupa (12oz)','Hot',100,30),(6,'Lupa (12oz)','Cold',110,30),
(6,'Dagat (16oz)','Hot',120,30),(6,'Dagat (16oz)','Cold',130,30),
(6,'Araw (22oz)','Hot',140,30),(6,'Araw (22oz)','Cold',150,30);

-- Product 7: 5th Brew Latte - Lupa/Dagat/Araw, Cold or Hot
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(7,'Lupa (12oz)','Hot',150,30),(7,'Lupa (12oz)','Cold',160,30),
(7,'Dagat (16oz)','Hot',170,30),(7,'Dagat (16oz)','Cold',180,30),
(7,'Araw (22oz)','Hot',190,30),(7,'Araw (22oz)','Cold',200,30);

-- Product 8: Tag-Ulan - Lupa/Dagat, Hot only
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(8,'Lupa (12oz)','Hot',140,30),
(8,'Dagat (16oz)','Hot',160,30);

-- Product 9: Tag-Init - Lupa/Dagat, Cold only
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(9,'Lupa (12oz)','Cold',140,30),
(9,'Dagat (16oz)','Cold',160,30);

-- Product 10: Binhi Latte - Lupa/Dagat/Araw, Cold Brew or Morning Brew
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(10,'Lupa (12oz)','Cold Brew',130,30),(10,'Lupa (12oz)','Morning Brew',130,30),
(10,'Dagat (16oz)','Cold Brew',150,30),(10,'Dagat (16oz)','Morning Brew',150,30),
(10,'Araw (22oz)','Cold Brew',170,30),(10,'Araw (22oz)','Morning Brew',170,30);

-- Product 11: Kalamansi Tea - Lupa/Dagat/Araw, Cold or Hot
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(11,'Lupa (12oz)','Cold',90,30),(11,'Lupa (12oz)','Hot',90,30),
(11,'Dagat (16oz)','Cold',110,30),(11,'Dagat (16oz)','Hot',110,30),
(11,'Araw (22oz)','Cold',130,30),(11,'Araw (22oz)','Hot',130,30);

-- Product 12: Tablea Latte - Lupa/Dagat, Cold or Hot
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(12,'Lupa (12oz)','Cold',130,30),(12,'Lupa (12oz)','Hot',130,30),
(12,'Dagat (16oz)','Cold',150,30),(12,'Dagat (16oz)','Hot',150,30);

-- Product 13: Asin Tibuok Latte SPECIAL - Lupa only, Cold or Hot
INSERT INTO product_options (product_id, size_label, temperature, price, stock) VALUES
(13,'Lupa (12oz)','Cold',180,20),
(13,'Lupa (12oz)','Hot',180,20);