CREATE TABLE IF NOT EXISTS flavors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  description VARCHAR(180) NOT NULL,
  emoji VARCHAR(8) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1
);
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  address VARCHAR(220) NOT NULL,
  district VARCHAR(80) NOT NULL,
  notes VARCHAR(300) DEFAULT NULL,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('nuevo','preparando','en camino','entregado','cancelado') NOT NULL DEFAULT 'nuevo',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  flavor_id INT UNSIGNED NOT NULL,
  size_name VARCHAR(50) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_flavor FOREIGN KEY (flavor_id) REFERENCES flavors(id)
);
INSERT INTO flavors (name, description, emoji) VALUES
('Lúcuma','Cremoso, peruano y con ese toque de caramelo.','🍨'),
('Chocolate','Intenso, suave y perfecto para cualquier momento.','🍫'),
('Fresa','Frutal, fresco y hecho para sonreír.','🍓'),
('Maracuyá','Ácido, tropical y muy refrescante.','🥭'),
('Vainilla','Clásico, aromático y deliciosamente cremoso.','🍦'),
('Menta choco','Refrescante con trocitos de chocolate.','🌿');
