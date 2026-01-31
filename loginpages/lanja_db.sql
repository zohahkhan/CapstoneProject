DROP DATABASE IF EXISTS lanja_db;
CREATE DATABASE lanja_db;
USE lanja_db;

CREATE TABLE CalendarEvent (
event_id		INT              	NOT NULL  						AUTO_INCREMENT,
event_title		VARCHAR(50)         NOT NULL, 
event_desc		TEXT		        NOT NULL, 
event_location	VARCHAR(250)		NOT NULL, 
event_date		DATETIME			NOT NULL, 
recurring		ENUM('Daily', 'Weekly', 'Monthly', 'Annually'), 
iterations		INT, 
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
password_hashed	VARCHAR(60)			NOT NULL, 
is_active		BOOLEAN				NOT NULL, 
joined_on		TIMESTAMP			NOT NULL, 
last_login		TIMESTAMP			NOT NULL, 
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



/* adding values to the database, personal info altered for privacy */ 

INSERT INTO User 
(
user_id,
first_name, 
last_name,		 
user_email,		 
user_phone, 
user_address, 
password_hashed, 
is_active, 
joined_on, 
last_login 
)

VALUES

(1, 'Carie', 'Baig', 'carie_b0009@email.com', '(217) 555-0101', '101 Maple Grove Drive Springfield, IL 62704', '$2y$10$kw5f0o4KGqtRwysU8o.Qn.HJIKgDfkctvyII75mYbsOKrJ9VFc7Rm', 1, '2022-03-15 08:12:45', '2023-07-21 14:55:12'), 

(2, 'Aliyah', 'Salah', 'sal11390@email.com', '(608) 555-0102', '245 Oak Valley Road Madison, WI 53711', '$2y$10$5LJs/YsrRj66QCORnDyNsOK.58J7p95UAc.FqnjVRccjgPlpl3pHy', 1, '2021-11-02 16:45:30', '2023-01-18 09:23:50'),

(3, 'Kamila', 'Nawaz', 'kamkam13506@email.com', '(919) 555-0103', '389 Pine Hill Lane Raleigh, NC 27607', '$2y$10$kBqOL5e1wDheS8DHTBl6bOKkpzjXM2hRWDqNM.KWjr/wifbhFYQni', 1, '2020-06-10 12:05:10', '2022-12-22 18:40:05'),

(4, 'Samiya', 'Rizzi', 'srizzi@email.com', '(626) 555-0104', '512 Sunset Ridge Avenue
Pasadena, CA 91105', '$2y$10$ps9ws6TzOeIV6pBZZDeEZOOBQnYIvDtDIsfE5cj.V6LNaPtH16.pS', 1, '2023-01-22 10:20:00', '2023-12-15 11:05:45'), 

(5, 'Roxanne', 'Sumar', 'rsumar@email.com', '(802) 555-0105', '76 Riverstone Court
Burlington, VT 05401', '$2y$10$0d4cPeCkCZhybZ6S1Dc.Q.gjAShuancxpzwDoWJkvch4znII/ftCi', 1, '2021-08-05 14:30:25', '2023-04-03 07:50:12'),

(6, 'Mina', 'Hashim', 'minahashim@email.com', '(972) 555-0106', '834 Willow Bend Way Plano, TX 75024', '$2y$10$QjUoa70XLel3.7dMehpBAOw.eazg.8udTQh8WVGP1336K.w70ANRG', 1, '2022-02-28 09:12:15', '2023-08-09 21:15:35'),

(7, 'Mariam', 'Latif', 'latimari@email.com', '(425) 555-0107', '690 Cedar Ridge Drive Bellevue, WA 98008', '$2y$10$S.8/Vr3.yfNkG3QApZoO1e.GtM7nfRJRalqGhTJAkeLKjdqZ1o1I6', 1, '2020-12-15 22:10:50', '2022-10-30 08:05:25'),

(8, 'Nadia', 'Maroon', 'mar00nn@email.com', '(614) 555-0108', '918 Meadowbrook Lane
Columbus, OH 43221', '$2y$10$YlawsO6QyO5qO0ezFK6H6OTkulwtUYsj8hJxSApG.S4gNyTsG.7Je', 1, '2021-05-19 07:45:40', '2023-03-12 19:35:50'), 

(9, 'Lula', 'Mirza', 'mirza_lu@email.com', '(847) 555-0109', '207 Lakeside Parkway
Evanston, IL 60202', '$2y$10$LLmgwqQ9NDqQKVu/ax3oaOgfmbSuQ62kg0Quuvxs46PCMPILGYPXq', 1, '2023-02-10 13:15:30', '2023-12-01 16:40:20'),

(10, 'Inaya', 'Sheikh', 'sheikh2005@email.com', '(480) 555-0110', '561 Highland Park Drive
Scottsdale, AZ 85255', '$2y$10$mUr9yZwVvkUoUwlqzmP4ieKXGH8b1kHqmsovCFo2UXzYcHR1/LHvq', 1, '2021-09-30 11:25:55', '2023-09-18 10:15:00'), 

(11, 'Jasmine', 'Taha', 'jazzy0267@email.com', '(919) 555-0111', '744 Brookhaven Road
Chapel Hill, NC 27516', '$2y$10$IZfz/u/54SqWzcb0otAq2eQb5uwOJm/X0jmJhrD3I21AlXnO3d3Bq', 1, '2022-07-07 17:40:20', '2023-06-11 22:55:10'), 

(12, 'Malay', 'Usami', 'musami89@email.com', '(707) 555-0112', '1205 Redwood Springs Circle
Santa Rosa, CA 95404', '$2y$10$PmNOQP2FrN50jE4ghiWCcuqI.FBsqto1syRl9TblNRZMtu6q6Ouz2', 1, '2020-03-22 06:55:10', '2022-11-08 13:10:25'), 

(13, 'Farah', 'Amin', 'faraha@email.com', '(828) 555-0113', '967 Autumn Crest Lane
Asheville, NC 28803', '$2y$10$tmhVscmPDY.eymVx1fAcbepfGLyI.aEOphCWBQ.vi0Bu4Qb2JnUEi', 1, '2021-12-25 19:05:45', '2023-08-05 17:30:15'),
 
(14, 'Sofie', 'Nasser', 'sofiebee23@email.com', '(609) 555-0114', '29 Stonegate Boulevard
Princeton, NJ 08540', '$2y$10$ncUkQZ.UpLK98sgHSx8iuemii1YVZcK3phdYkRdTh0G6Hs3kowcim', 1, '2021-01-18 15:30:40', '2023-05-25 12:45:35'), 

(15, 'Fatima', 'Noore', 'f_noore3342@email.com', '(410) 555-0115', '392 Harbor Point Drive
Annapolis, MD 21403', '$2y$10$1Ck3Dmd3lE7drK/MIomR0.WEl9Wzo6aj2ec0vP5PBwWPwVAJiYqlm', 1, '2022-09-05 09:15:25', '2023-10-29 08:05:55'), 
 
(16, 'Amara', 'Noore', 'a_noore1207@email.com', '(410) 555-0116', '392 Harbor Point Drive
Annapolis, MD 21403', '$2y$10$yeNHHp5i25BeyAcXPyQvMuG/l4ib0opBdsff9ES50Dd3PLQF/Ufk2', 1, '2022-09-05 09:40:00', '2022-09-20 14:30:45'),

(17, 'Zoha', 'K', 'kha27882@email.com', '(406) 555-0117', '155 Golden Meadow Drive
Bozeman, MT 59718', '$2y$10$hugC3ImjPgD9yz4Xw.ZqTO0bHkj1P1MkzdREMnH7c/xVF.vCec3L2', 1, '2023-03-12 12:55:30', '2023-12-03 20:25:50'),
     
(18, 'JJ', 'G', 'gil42134@email.com', '(941) 555-0118', '880 Cypress Hollow Road
Sarasota, FL 34232', '$2y$10$ocq4zYQSUKbqVXvY/GZeWOfBWslM09JPpgXokfiJ8RJqWPPy28Tke', 1, '2021-06-28 18:20:15', '2023-07-15 09:05:40'),
    
(19, 'Kah', 'O', 'ong92990@email.com', '(970) 555-0119', '602 Juniper Ridge Lane
Fort Collins, CO 80525', '$2y$10$f/2.BUqrR7y7NdK.OSOU1uHZ3puYmPzIAHUaFia4pas9FZCdT4lYG', 1, '2022-04-09 10:10:50', '2023-09-22 15:50:25'),  
     
(20, 'Shan', 'K', 'kat44977@email.com', '(859) 555-0120', '2173 Bluebird Crossing
Lexington, KY 40503', '$2y$10$/kIYD9Ryl1u.T65I6n.AgeK8wW8i7Q4Ca1v.YPkUdLhwSum6Ip0hK', 1, '2020-11-14 08:35:20', '2022-12-18 11:45:10');
   

INSERT INTO Role 
(
role_id,
role_name,
role_desc
)
VALUES
(1, 'President', 'Oversees the entire organization, makes final decisions, and ensures overall system and organizational integrity.'),
(2, 'Department Head', 'Manages the department’s operations, reporting, and member activities, and serves as the primary liaison to the President.'),
(3, 'Member', 'Participates in organizational activities, submits required forms, engages with events and communications.'),
(4, 'Admin/Maintenance', 'Maintains the technical health, security, and configuration of the system without participating in organizational decision-making.');


INSERT INTO UserRole 
(
user_id,
role_id	
)
VALUE
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 2),
(17, 3),
(17, 4),
(18, 4),
(19, 4),
(20, 4);


INSERT INTO Department 
(
dept_id,
user_id,
dept_name,
dept_desc
)
VALUES
(1, 17, 'Reporting Department', 'The Reporting Department is responsible for collecting, organizing, analyzing, and consolidating organizational data into accurate reports.');


INSERT INTO CalendarEvent 
(
event_id,
event_title,
event_desc,
event_location,
event_date,
recurring, 
iterations,
created_at
)
VALUES
(1, 'First Day of Class', 'The first day of Spring Term begins.', 'D2L', '2026-01-20 00:00:00', NULL, NULL, '2026-01-08 14:42:45'),   
(2, 'Team Meeting', 'Gather to discuss future developments.', 'Zoom', '2026-01-23 12:30:00', 'Once', 15, '2026-01-08 14:48:31'); 


INSERT INTO Permission 
(
permission_id,
perm_title, 
perm_desc, 
perm_resource, 
perm_crud
)
VALUES

/* president only permissions */
			-- pres only - user 
(1, 'Create User Account', 'Create a new member account by entering required credentials and assigning an initial active status.', 'User', 'Create'),
(2, 'Update User Account Status', 'Freeze or reactivate user accounts, immediately revoking or restoring login access while preserving data.', 'User', 'Update'), 
(3, 'View User Accounts', 'View all user accounts and related metadata for monitoring and management.', 'User', 'Read'),
(4, 'View User Activity Logs', 'View user engagement metrics including login frequency, last login time, and site activity.', 'User', 'Read'),
			-- pres only - suggestion
(5, 'Receive Message Requests', 'Receive and view membership requests submitted by visitors via contact form.', 'Suggestion', 'Read'),
(6, 'Respond to Requests', 'Accept or decline visitor membership requests.', 'Suggestion', 'Update'),
			-- pres only - event
(7, 'Cancel Calendar Event', 'Cancel any calendar event or recurring series across the organization.', 'Calendar Event', 'Update'),

/* leadership = pres & dept head perm */
			-- leader - docs
(8, 'Upload Document', 'Upload documents (PDF/DOCX) to the system within size and performance constraints.', 'Document', 'Create'),
(9, 'Set Document Visibility', 'Define which roles or departments can view and download a document.', 'Document', 'Update'),
(10, 'View Documents', 'View and download documents based on assigned visibility permissions.', 'Document', 'Read'),
(11, 'Archive Document', 'Archive documents so they are no longer active but retained for historical reference.', 'Document', 'Update'),
			-- leader - events
(12, 'Create Calendar Event', 'Create calendar events that are visible to selected roles within seconds of posting.', 'Calendar Event', 'Create'),
(13, 'Update Calendar Event', 'Edit calendar events according to defined modification permissions, overwriting displayed versions while logging changes.', 'Calendar Event', 'Update'),
			-- leader - announce
(14, 'Publish Announcement', 'Create and publish announcements with title, body, and expiry date.', 'Announcement', 'Create'),
(15, 'Update Announcement', 'Edit active announcements; expired announcements auto-hide while status updates accordingly.', 'Announcement', 'Update'),
(16, 'View Announcement Archive', 'Archive any announcement across the organization to preserve history without deletion.', 'Announcement', 'Update'),
			-- leader - other 
(17, 'View Reports', 'View monthly aggregated reports with visualizations and qualitative interpretations.', 'Form Response', 'Read'),

/* dept head only */
(18, 'Cancel Calendar Event', 'Cancel calendar events they created or in their department only.', 'Calendar Event', 'Update'),

/* regular member only */
(19, 'View Own Profile', 'View personal profile information including name, contact details, role, and available navigation options.', 'User', 'Read'),
(20, 'Update Own Profile', 'Update personal profile information such as name, email, phone number, and address.', 'User', 'Update'),
(21, 'Submit Form Response', 'Complete and submit a survey or form related to member involvement.', 'Form Response', 'Create'),
(22, 'Update Own Form Response', 'Edit a previously submitted survey response before the submission deadline to correct errors.', 'Form Response', 'Update'),
(23, 'View Own Form Responses', 'View a list of previously submitted form responses and open individual submissions in read-only mode.', 'Form Response', 'Read'),
(24, 'View Member Announcements', 'View published announcements intended for members.', 'Announcement', 'Read'),

/* applies to everyone */
(25, 'Submit Message Suggestions', 'Visitors and all members can send membership requests and/or suggestions via contact form.', 'Suggestion', 'Create'),
(26, 'Reset Password', 'Reset account password using a time-limited, tokenized email link with enforced password strength rules.', 'User', 'Update'),
			-- all member levels, excludes admins
(27, 'View Calendar Events', 'View all calendar events across roles with consistent formatting.', 'Calendar Event', 'Read'), 
(28, 'View Announcements', 'View announcements and their current status (active/expired).', 'Announcement', 'Read'), 

/* admin only */
(29, 'View System Logs', 'View system log entries for monitoring and troubleshooting.', 'Audit Log', 'Read'),
(30, 'Export System Logs', 'Generate and export system logs as CSV or PDF files using scheduled scripts for auditing and archival purposes.', 'Audit Log', 'Read'),
(31, 'Assign User Roles', 'Assign or change user roles (President, Dept Head, Member, Admin) with immediate effect and audit logging of changes.', 'User Role', 'Update'),
(32, 'Grant Role Permissions', 'Grant or modify CRUD permissions associated with roles to control system access.', 'Role Permission', 'Update'),
(33, 'View Performance Metrics', 'View system performance metrics and operational data through an administrative dashboard.', 'Audit Log', 'Read'),
(34, 'View System Alerts', 'View system alerts and notifications related to failures or downtime within the past 90 days.', 'Audit Log', 'Read');


INSERT INTO RolePermission 
(
roleperm_id,
permission_id, 
role_id
)
VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),

(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),

(18, 8, 2),
(19, 9, 2),
(20, 10, 2),
(21, 11, 2),
(22, 12, 2),
(23, 13, 2),
(24, 14, 2),
(25, 15, 2),
(26, 16, 2),
(27, 17, 2),

(28, 18, 2),

(29, 19, 3),
(30, 20, 3),
(31, 21, 3),
(32, 22, 3),
(33, 23, 3),
(34, 24, 3),

(35, 25, 1),
(36, 26, 1),
(37, 25, 2),
(38, 26, 2),
(39, 25, 3),
(40, 26, 3),
(41, 25, 4),
(42, 26, 4),

(43, 27, 1),
(44, 28, 1),
(45, 27, 2),
(46, 28, 2),
(47, 27, 3),
(48, 28, 3),

(49, 29, 4),
(50, 30, 4),
(51, 31, 4),
(52, 32, 4),
(53, 33, 4),
(54, 34, 4);

/* to access the database */
GRANT SELECT, INSERT, UPDATE
ON lanja_db.*
TO mgs_user@localhost
IDENTIFIED BY 'pa55word';



