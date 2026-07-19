USE kahuna;

-- Client login:
-- email: client@kahuna.com
-- password: Client123

-- Admin login:
-- email: admin@kahuna.com
-- password: Admin123

INSERT INTO Users (name, surname, email, password, role) VALUES
('Client', 'User', 'client@kahuna.com', '$2y$10$5BnBHDzwiyMoiSw2u93x5uXrmn7ULFl3TSBso7l9bRJAaTJ3Ouyyq', 'client'),
('Admin', 'User', 'admin@kahuna.com', '$2y$10$oAJuGd4j6yeP1bhI89g9ue/daoKTJdmUHrh8AeXEc9DTiT/njFioy', 'admin')
-- if users exist, update instead of failing
ON DUPLICATE KEY UPDATE
name = VALUES(name),
surname = VALUES(surname),
password = VALUES(password),
role = VALUES(role);