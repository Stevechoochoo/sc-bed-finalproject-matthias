USE kahuna;

-- Client login:
-- email: client@kahuna.com
-- password: Client123

-- Admin login:
-- email: admin@kahuna.com
-- password: Admin123

INSERT INTO Users (name, surname, email, password, role) VALUES
('Client', 'User', 'client@kahuna.com', 'Client123', 'client'),
('Admin', 'User', 'admin@kahuna.com', 'Admin123', 'admin');