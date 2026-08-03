CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  department VARCHAR(50) NOT NULL,
  position VARCHAR(50) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Aktif',
  photo VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Masukkan data admin dummy (password adalah 'admin123')
INSERT INTO admin (username, password) VALUES ('admin', '$2y$12$SiycjCpeahMtLrnok37gcOLwD4FSbkJhrdNBxVC71y91Ti0jwxyte');

-- Masukkan data pegawai dummy
INSERT INTO employees (name, email, department, position, status, photo) VALUES 
('Angga', 'angga@example.com', 'IT', 'Developer', 'Aktif', NULL),
('Budi', 'budi@example.com', 'HR', 'Manager', 'Aktif', NULL),
('Citra', 'citra@example.com', 'Finance', 'Staff', 'Nonaktif', NULL);
