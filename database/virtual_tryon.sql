CREATE DATABASE IF NOT EXISTS virtual_tryon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE virtual_tryon;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(80) NOT NULL,
  price DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
  description TEXT NULL,
  product_image VARCHAR(255) NULL,
  virtual_tryon_image VARCHAR(255) NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_products_status_created (status, created_at)
) ENGINE=InnoDB;

CREATE TABLE tryon_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  screenshot_path VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tryon_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tryon_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_tryon_user_created (user_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE favorites (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_favorite (user_id, product_id),
  CONSTRAINT fk_favorite_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_favorite_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Demo admin password: Admin@123 (change immediately after first login).
INSERT INTO users (name, email, password, is_admin) VALUES
('Maison Administrator', 'admin@maison.local', '$2y$10$qBq00vv3ip7DF2ftcqAKeeYHEb6Bewz1CDn.g6N5H7l.37wF7ieOa', 1);

INSERT INTO products (name, category, price, description, product_image, virtual_tryon_image, status) VALUES
('Bordeaux Wrap Dress', 'Evening Edit', 8490.00, 'A fluid satin wrap silhouette with a sculpted waist and softly draped skirt.', 'uploads/products/burgundy-wrap.png', 'uploads/virtual_tryon/burgundy-wrap.png', 1),
('Nocturne Cocktail Dress', 'After Dark', 9250.00, 'A refined midnight column dress with long sleeves and an asymmetric draped detail.', 'uploads/products/navy-cocktail.png', 'uploads/virtual_tryon/navy-cocktail.png', 1);
