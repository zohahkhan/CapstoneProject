CREATE DATABASE IF NOT EXISTS lanja_db;
USE lanja_db;

CREATE TABLE IF NOT EXISTS User (
user_id			INT					NOT NULL AUTO_INCREMENT,
first_name		VARCHAR(25)			NOT NULL, 
last_name		VARCHAR(25)			NOT NULL, 
user_email		VARCHAR(50)			NOT NULL, 
user_phone		VARCHAR(15)			NOT NULL, 
user_address	VARCHAR(200)			NOT NULL, 
password_hashed	VARCHAR(60)			NOT NULL, 
is_active		BOOLEAN				NOT NULL, 
joined_on		TIMESTAMP			NOT NULL, 
last_login		TIMESTAMP			NOT NULL, 
PRIMARY KEY (user_id), 
UNIQUE INDEX user_email (user_email)
);

CREATE TABLE IF NOT EXISTS PasswordResetToken (
reset_id 		INT					NOT NULL AUTO_INCREMENT,
user_id			INT					NOT NULL, 
token			VARCHAR(15)			NOT NULL, 
reset_success	BOOLEAN				NOT NULL,
expires_at		TIMESTAMP			NOT NULL, 
used_at		    TIMESTAMP,
PRIMARY KEY (reset_id),
INDEX user_id (user_id), 	
FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE TABLE IF NOT EXISTS Role (
role_id			INT					NOT NULL AUTO_INCREMENT,
role_name		VARCHAR(50)			NOT NULL,
PRIMARY KEY (role_id),
UNIQUE INDEX role_name (role_name)
);

CREATE TABLE IF NOT EXISTS UserRole (
user_role_id	INT					NOT NULL AUTO_INCREMENT,
user_id			INT					NOT NULL,
role_id			INT					NOT NULL,
assigned_at		TIMESTAMP			NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (user_role_id),
INDEX user_id (user_id),
INDEX role_id (role_id),
FOREIGN KEY (user_id) REFERENCES User (user_id),
FOREIGN KEY (role_id) REFERENCES Role (role_id),
UNIQUE INDEX user_role_unique (user_id, role_id)
);

CREATE TABLE IF NOT EXISTS Permission (
permission_id	INT					NOT NULL AUTO_INCREMENT,
permission_name	VARCHAR(50)			NOT NULL,
PRIMARY KEY (permission_id),
UNIQUE INDEX permission_name (permission_name)
);

CREATE TABLE IF NOT EXISTS RolePermission (
role_permission_id	INT				NOT NULL AUTO_INCREMENT,
role_id			INT					NOT NULL,
permission_id	INT					NOT NULL,
PRIMARY KEY (role_permission_id),
INDEX role_id (role_id),
INDEX permission_id (permission_id),
FOREIGN KEY (role_id) REFERENCES Role (role_id),
FOREIGN KEY (permission_id) REFERENCES Permission (permission_id),
UNIQUE INDEX role_permission_unique (role_id, permission_id)
);

CREATE TABLE IF NOT EXISTS RoleChangeLog (
log_id			INT					NOT NULL AUTO_INCREMENT,
user_id			INT					NOT NULL,
admin_id		INT					NOT NULL,
old_role_id		INT,
new_role_id		INT					NOT NULL,
changed_at		TIMESTAMP			NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (log_id),
INDEX user_id (user_id),
INDEX admin_id (admin_id),
FOREIGN KEY (user_id) REFERENCES User (user_id),
FOREIGN KEY (admin_id) REFERENCES User (user_id),
FOREIGN KEY (old_role_id) REFERENCES Role (role_id),
FOREIGN KEY (new_role_id) REFERENCES Role (role_id)
);

INSERT INTO User 
(user_id, first_name, last_name, user_email, user_phone, user_address, password_hashed, is_active, joined_on, last_login)
VALUES
(1, 'Carie', 'Baig', 'carie_b0009@email.com', '(217) 555-0101', '101 Maple Grove Drive Springfield, IL 62704', '$2y$10$kw5f0o4KGqtRwysU8o.Qn.HJIKgDfkctvyII75mYbsOKrJ9VFc7Rm', 1, '2022-03-15 08:12:45', '2023-07-21 14:55:12'), 
(17, 'Zoha', 'K', 'kha27882@email.com', '(406) 555-0117', '155 Golden Meadow Drive Bozeman, MT 59718', '$2y$10$hugC3ImjPgD9yz4Xw.ZqTO0bHkj1P1MkzdREMnH7c/xVF.vCec3L2', 1, '2023-03-12 12:55:30', '2023-12-03 20:25:50'),
(18, 'JJ', 'G', 'gil42134@email.com', '(941) 555-0118', '880 Cypress Hollow Road Sarasota, FL 34232', '$2y$10$ocq4zYQSUKbqVXvY/GZeWOfBWslM09JPpgXokfiJ8RJqWPPy28Tke', 1, '2021-06-28 18:20:15', '2023-07-15 09:05:40'),
(19, 'Kah', 'O', 'ong92990@email.com', '(970) 555-0119', '602 Juniper Ridge Lane Fort Collins, CO 80525', '$2y$10$f/2.BUqrR7y7NdK.OSOU1uHZ3puYmPzIAHUaFia4pas9FZCdT4lYG', 1, '2022-04-09 10:10:50', '2023-09-22 15:50:25'),  
(20, 'Shan', 'K', 'kat44977@email.com', '(859) 555-0120', '2173 Bluebird Crossing Lexington, KY 40503', '$2y$10$/kIYD9Ryl1u.T65I6n.AgeK8wW8i7Q4Ca1v.YPkUdLhwSum6Ip0hK', 1, '2020-11-14 08:35:20', '2022-12-18 11:45:10');

INSERT INTO Role (role_id, role_name)
VALUES
(1, 'President'),
(2, 'Dept Head'),
(3, 'Member'),
(4, 'Admin');

INSERT INTO Permission (permission_id, permission_name)
VALUES
(1, 'Create'),
(2, 'Read'),
(3, 'Update'),
(4, 'Delete');

INSERT INTO RolePermission (role_id, permission_id)
VALUES
(1, 1), (1, 2), (1, 3), (1, 4),
(2, 1), (2, 2), (2, 3),
(3, 2),
(4, 1), (4, 2), (4, 3), (4, 4);

INSERT INTO UserRole (user_id, role_id)
VALUES
(1, 4),
(17, 4),
(18, 4),
(19, 4),
(20, 4);

GRANT SELECT, INSERT, UPDATE, DELETE ON lanja_db.* TO 'mgs_user'@'localhost' IDENTIFIED BY 'pa55word';