DROP DATABASE IF EXISTS lanja_db;
CREATE DATABASE lanja_db;
USE lanja_db;

CREATE TABLE CalendarEvent (
event_id		INT              	NOT NULL  						AUTO_INCREMENT,
event_title		VARCHAR(50)         NOT NULL, 
event_desc		TEXT		        NOT NULL, 
event_location	VARCHAR(250)		NOT NULL, 
event_date		DATETIME			NOT NULL, 
recurring		ENUM('Daily', 'Weekly', 'Monthly', 'Annually')		NOT NULL, 
iterations		INT					NOT NULL, 
created_at		TIMESTAMP			NOT NULL, 
updated_at		TIMESTAMP,
deleted_at		TIMESTAMP,
PRIMARY KEY (event_id)
);

CREATE TABLE User (
user_id			INT					NOT NULL  						AUTO_INCREMENT,
first_name		VARCHAR(25)			NOT NULL, 
last_name		VARCHAR(25)			NOT NULL, 
user_email		VARCHAR(50)			NOT NULL, 
user_phone		VARCHAR(15)			NOT NULL, 
user_address	VARCHAR(200)			NOT NULL, 
password_hash	VARCHAR(50)			NOT NULL, 
is_active		BOOLEAN				NOT NULL, 
last_login		TIMESTAMP			NOT NULL, 
joined_on		TIMESTAMP			NOT NULL, 
PRIMARY KEY (user_id), 
UNIQUE INDEX user_email (user_email)
);

CREATE TABLE Attendance (
attendance_id	INT					NOT NULL  						AUTO_INCREMENT,
user_id 		INT					NOT NULL, 
event_id		INT					NOT NULL, 
attend_status	ENUM('Present', 'Absent', 'Late', 'Excused')		NOT NULL, 
check_in_time	DATETIME			NOT NULL, 
taken_by		INT					NOT NULL, 
taken_at		VARCHAR(100)			NOT NULL, 
notes			TEXT				NOT NULL, 
PRIMARY KEY (attendance_id), 
INDEX user_id (user_id), 
INDEX event_id (event_id), 
FOREIGN KEY (user_id) REFERENCES User (user_id),
FOREIGN KEY (taken_by) REFERENCES User (user_id),
FOREIGN KEY (event_id) REFERENCES CalendarEvent (event_id)
);

CREATE TABLE FormTemplate (
template_id		INT			 		NOT NULL  						AUTO_INCREMENT,
temp_title		VARCHAR(50)			NOT NULL, 
temp_desc		TEXT				NOT NULL, 
temp_status		ENUM('Draft', 'Active', 'Archived')					NOT NULL, 
form_questions	JSON				NOT NULL, 
form_deadline	DATETIME			NOT NULL, 
updated_at		TIMESTAMP,
deleted_at		TIMESTAMP,
PRIMARY KEY (template_id)
);

CREATE TABLE FormResponse (
response_id 	INT					NOT NULL  						AUTO_INCREMENT,
template_id		INT					NOT NULL, 
user_id			INT					NOT NULL, 
form_response	JSON				NOT NULL, 
form_status		ENUM('Pending', 'Reviewed', 'Finalized')			NOT NULL, 
PRIMARY KEY (response_id), 
INDEX template_id (template_id), 
INDEX user_id (user_id), 
FOREIGN KEY (template_id) REFERENCES FormTemplate (template_id),
FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE TABLE PasswordResetToken (
reset_id 		INT					NOT NULL  						AUTO_INCREMENT,
user_id			INT					NOT NULL, 
token			VARCHAR(15)			NOT NULL, 
reset_success	BOOLEAN				NOT NULL,
expires_at		TIMESTAMP			NOT NULL, 
used_at		TIMESTAMP,
PRIMARY KEY (reset_id),
INDEX user_id (user_id), 	
FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE TABLE Permission (
permission_id 	INT					NOT NULL  						AUTO_INCREMENT,
perm_title		VARCHAR(50)			NOT NULL, 
perm_desc		TEXT				NOT NULL, 
perm_resource	VARCHAR(100)		NOT NULL, 
perm_crud		ENUM('Create', 'Read', 'Update', 'Delete')	NOT NULL, 
PRIMARY KEY (permission_id)
);

CREATE TABLE Role (
role_id			INT					NOT NULL  						AUTO_INCREMENT,
role_name		VARCHAR(50)			NOT NULL, 
role_desc		TEXT				NOT NULL, 
PRIMARY KEY (role_id)	
);

CREATE TABLE RolePermission (
roleperm_id		INT					NOT NULL  						AUTO_INCREMENT,
permission_id	INT					NOT NULL, 
role_id			INT					NOT NULL, 
PRIMARY KEY (roleperm_id),
INDEX permission_id (permission_id),
INDEX role_id (role_id),
FOREIGN KEY (permission_id) REFERENCES Permission (permission_id),
FOREIGN KEY (role_id) REFERENCES Role (role_id)
);

CREATE TABLE UserRole (
user_id			INT					NOT NULL, 
role_id			INT					NOT NULL, 
PRIMARY KEY (user_id, role_id),
INDEX user_id (user_id),
INDEX role_id (role_id),
FOREIGN KEY (user_id) REFERENCES User (user_id),
FOREIGN KEY (role_id) REFERENCES Role (role_id)
);

CREATE TABLE Department (
dept_id			INT					NOT NULL  						AUTO_INCREMENT,
user_id			INT					NOT NULL, 
dept_name		VARCHAR(50)			NOT NULL, 
dept_desc		TEXT				NOT NULL, 
PRIMARY KEY (dept_id),
INDEX user_id (user_id),
FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE TABLE Document (
document_id		INT					NOT NULL  						AUTO_INCREMENT,
uploaded_by		INT					NOT NULL, 
visibility_scope	ENUM('Members', 'Dept Heads', 'Everyone')		NOT NULL, 
dept_id			INT,
doc_title		VARCHAR(50)			NOT NULL, 
stored_url		VARCHAR(250)		NOT NULL, 
archived		BOOLEAN				NOT NULL, 
created_at		TIMESTAMP			NOT NULL, 
updated_at		TIMESTAMP,
deleted_at		TIMESTAMP,
PRIMARY KEY (document_id),
INDEX dept_id (dept_id),
INDEX user_id (uploaded_by),
FOREIGN KEY (uploaded_by) REFERENCES User (user_id),
FOREIGN KEY (dept_id) REFERENCES Department (dept_id)
);

CREATE TABLE Announcement (
announcement_id		INT				NOT NULL  						AUTO_INCREMENT,
user_id				INT				NOT NULL, 
dept_id				INT,
visibility_scope	ENUM('Members', 'Dept Heads', 'Everyone')		NOT NULL, 
announce_title		VARCHAR(50)		NOT NULL, 
announce_body		TEXT			NOT NULL, 
announce_expiry		DATETIME		NOT NULL, 
allow_opt_out		BOOLEAN			NOT NULL, 
announce_delivery	TIMESTAMP		NOT NULL, 
archived			BOOLEAN			NOT NULL, 
created_at			TIMESTAMP		NOT NULL, 
updated_at			TIMESTAMP,
PRIMARY KEY (announcement_id),
INDEX user_id (user_id),
INDEX dept_id (dept_id),
FOREIGN KEY (user_id) REFERENCES User (user_id),
FOREIGN KEY (dept_id) REFERENCES Department (dept_id)
);

CREATE TABLE AuditLog (
log_id			INT					NOT NULL  						AUTO_INCREMENT,
user_id			INT					NOT NULL, 
action			ENUM('Create', 'Update', 'Delete', 'Archive')		NOT NULL, 
entity_type		VARCHAR(50)			NOT NULL, 
entity_id		INT					NOT NULL, 
before_json		JSON				NOT NULL, 
after_json		JSON				NOT NULL, 
occurred_at		TIMESTAMP			NOT NULL, 
PRIMARY KEY (log_id),
INDEX user_id (user_id),
INDEX entity_id (entity_id),
FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE TABLE Suggestion (
suggestion_id	INT					NOT NULL  						AUTO_INCREMENT,
full_name		VARCHAR(50)			NOT NULL, 
contact_email	VARCHAR(50)			NOT NULL, 
visitor_msg		TEXT				NOT NULL, 
msg_status		ENUM('Pending', 'Reviewed', 'Finalized')			NOT NULL, 
session_id		VARCHAR(50)			NOT NULL, 
created_at		TIMESTAMP			NOT NULL, 
PRIMARY KEY (suggestion_id)
);







