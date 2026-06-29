CREATE DATABASE IF NOT EXISTS kahuna;

USE kahuna;


-- Tables Creation

CREATE TABLE Products
(
  product_id    INT          NOT NULL AUTO_INCREMENT,
  serial_number VARCHAR(50)  NOT NULL,
  product_name  VARCHAR(255) NOT NULL,
  warranty      INT          NOT NULL,
  PRIMARY KEY (product_id)
);

ALTER TABLE Products
  ADD CONSTRAINT UQ_product_id UNIQUE (product_id);

ALTER TABLE Products
  ADD CONSTRAINT UQ_serial_number UNIQUE (serial_number);

CREATE TABLE RegisteredProducts
(
  rp_id         INT  NOT NULL AUTO_INCREMENT,
  user_id       INT  NOT NULL,
  product_id    INT  NOT NULL,
  purchase_date DATE NOT NULL,
  PRIMARY KEY (rp_id)
);

ALTER TABLE RegisteredProducts
  ADD CONSTRAINT UQ_rp_id UNIQUE (rp_id);

CREATE TABLE Users
(
  user_id  INT                    NOT NULL AUTO_INCREMENT,
  name     VARCHAR(100)           NOT NULL,
  surname  VARCHAR(100)           NOT NULL,
  email    VARCHAR(255)           NOT NULL,
  password VARCHAR(255)           NOT NULL,
  role     ENUM('client','admin') NOT NULL DEFAULT 'client',
  token    VARCHAR(255)           NULL    ,
  PRIMARY KEY (user_id)
);

ALTER TABLE Users
  ADD CONSTRAINT UQ_user_id UNIQUE (user_id);

ALTER TABLE Users
  ADD CONSTRAINT UQ_email UNIQUE (email);

ALTER TABLE RegisteredProducts
  ADD CONSTRAINT FK_Users_TO_RegisteredProducts
    FOREIGN KEY (user_id)
    REFERENCES Users (user_id);

ALTER TABLE RegisteredProducts
  ADD CONSTRAINT FK_Products_TO_RegisteredProducts
    FOREIGN KEY (product_id)
    REFERENCES Products (product_id);

-- Product Table data insertion

INSERT INTO Products (serial_number, product_name, warranty) VALUES
('KHWM8199911', 'CombiSpin Washing Machine', 2),
('KHWM8199912', 'CombiSpin + Dry Washing Machine', 2),
('KHMW789991', 'CombiGrill Microwave', 1),
('KHWP890001', 'K5 Water Pump', 5),
('KHWP890002', 'K5 Heated Water Pump', 5),
('KHSS988881', 'Smart Switch Lite', 2),
('KHSS988882', 'Smart Switch Pro', 2),
('KHSS988883', 'Smart Switch Pro V2', 2),
('KHHM89762', 'Smart Heated Mug', 1),
('KHSB0001', 'Smart Bulb 001', 1);