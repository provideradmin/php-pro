CREATE DATABASE IF NOT EXISTS carmaster_db;
USE carmaster_db;

CREATE TABLE IF NOT EXISTS clients (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       name VARCHAR(255),
                                       email VARCHAR(255),
                                       phone VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS cars (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    client_id INT UNSIGNED,
                                    FOREIGN KEY (client_id) REFERENCES clients(id),
                                    type VARCHAR(255),
                                    brand VARCHAR(255),
                                    model VARCHAR(255),
                                    year INT UNSIGNED,
                                    number VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS services (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        name VARCHAR(255),
                                        cost FLOAT UNSIGNED,
                                        duration INT UNSIGNED
);

CREATE TABLE IF NOT EXISTS parts (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     name VARCHAR(255),
                                     cost FLOAT UNSIGNED,
                                     quantity INT UNSIGNED,
                                     selling_price FLOAT UNSIGNED
);

CREATE TABLE IF NOT EXISTS materials (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         name VARCHAR(255),
                                         cost FLOAT UNSIGNED,
                                         quantity INT UNSIGNED
);

CREATE TABLE IF NOT EXISTS orders (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      order_number VARCHAR(255),
                                      creation_date DATETIME,
                                      client_id INT UNSIGNED,
                                      FOREIGN KEY (client_id) REFERENCES clients(id),
                                      car_id INT UNSIGNED,
                                      FOREIGN KEY (car_id) REFERENCES cars(id),
                                      service_id INT UNSIGNED,
                                      FOREIGN KEY (service_id) REFERENCES services(id),
                                      total_cost FLOAT UNSIGNED,
                                      payment_date DATETIME
);

CREATE TABLE IF NOT EXISTS order_parts (
                                           order_id INT UNSIGNED,
                                           FOREIGN KEY (order_id) REFERENCES orders(id),
                                           part_id INT UNSIGNED,
                                           FOREIGN KEY (part_id) REFERENCES parts(id),
                                           quantity INT UNSIGNED,
                                           PRIMARY KEY (order_id, part_id)
);

CREATE TABLE IF NOT EXISTS order_materials (
                                               order_id INT UNSIGNED,
                                               FOREIGN KEY (order_id) REFERENCES orders(id),
                                               material_id INT UNSIGNED,
                                               FOREIGN KEY (material_id) REFERENCES materials(id),
                                               quantity INT UNSIGNED,
                                               PRIMARY KEY (order_id, material_id)
);

CREATE TABLE IF NOT EXISTS payments (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        order_id INT UNSIGNED,
                                        FOREIGN KEY (order_id) REFERENCES orders(id),
                                        status VARCHAR(255),
                                        payment_date DATETIME
);
