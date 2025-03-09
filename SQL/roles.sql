CREATE TABLE roles (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  role VARCHAR(255) NOT NULL UNIQUE,
  role_description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserting roles into the roles table
INSERT INTO roles (role_name, role_description)
VALUES
  ('Super Admin', 'Has full access to all aspects of the system and can create and manage admins.'),
  ('Admin', 'Can manage teams, projects, and other users within their own company.'),
  ('Manager', 'Manages projects and tasks within a team.'),
  ('Assistant Manager', 'Assists the manager in managing projects and tasks.'),
  ('User', 'Has access to specific tasks and projects assigned to them.'),
  ('Guest', 'Limited access to view tasks and projects but cannot make any changes.');
