DROP DATABASE IF EXISTS lanja_db;
CREATE DATABASE IF NOT EXISTS lanja_db;
USE lanja_db;


CREATE TABLE User (
user_id			INT					NOT NULL  						AUTO_INCREMENT,
first_name		VARCHAR(25)			NOT NULL, 
last_name		VARCHAR(25)			NOT NULL, 
user_email		VARCHAR(50)			NOT NULL, 
user_phone		VARCHAR(15)			NOT NULL, 
user_address	VARCHAR(200)		NOT NULL, 
password_hashed	VARCHAR(60)			NOT NULL, 
is_active		BOOLEAN				NOT NULL, 
joined_on		TIMESTAMP			NOT NULL   					 DEFAULT CURRENT_TIMESTAMP, 
last_login		TIMESTAMP			NOT NULL					 DEFAULT CURRENT_TIMESTAMP, 
last_updated	TIMESTAMP									  	 ON UPDATE CURRENT_TIMESTAMP,
updated_by		INT,
PRIMARY KEY (user_id), 
UNIQUE INDEX user_email (user_email)
);

CREATE TABLE CalendarEvent (
event_id		INT              	NOT NULL  						AUTO_INCREMENT,
event_title		VARCHAR(50)         NOT NULL, 
event_desc		TEXT		        NOT NULL, 
event_location	VARCHAR(250)		NOT NULL, 
event_date		DATETIME			NOT NULL, 
created_at		TIMESTAMP			NOT NULL					DEFAULT CURRENT_TIMESTAMP, 
created_by		INT					NOT NULL,
updated_at		TIMESTAMP										ON UPDATE CURRENT_TIMESTAMP,
updated_by		INT,
PRIMARY KEY (event_id),
FOREIGN KEY (created_by) REFERENCES User (user_id),
FOREIGN KEY (updated_by) REFERENCES User (user_id)
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
form_status		ENUM('Pending', 'Reviewed', 'Finalized')			NOT NULL , 
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
role_id			INT					NOT NULL,
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
created_at		TIMESTAMP			NOT NULL						DEFAULT CURRENT_TIMESTAMP, 
created_by		INT					NOT NULL,
updated_at		TIMESTAMP											ON UPDATE CURRENT_TIMESTAMP,
updated_by		INT,
deleted_at		TIMESTAMP,
PRIMARY KEY (document_id),
INDEX dept_id (dept_id),
INDEX user_id (uploaded_by),
FOREIGN KEY (uploaded_by) REFERENCES User (user_id),
FOREIGN KEY (dept_id) REFERENCES Department (dept_id)
);

CREATE TABLE Announcement (
announcement_id		INT				NOT NULL  						AUTO_INCREMENT,
dept_id				INT,
visibility_scope	ENUM('Members', 'Dept Heads', 'Everyone')		NOT NULL, 
announce_title		VARCHAR(50)		NOT NULL, 
announce_body		TEXT			NOT NULL, 
announce_expiry		DATETIME		NOT NULL, 
allow_opt_out		BOOLEAN			NOT NULL, 
announce_delivery	TIMESTAMP		NOT NULL, 
archived			BOOLEAN			NOT NULL, 
created_at		TIMESTAMP			NOT NULL						DEFAULT CURRENT_TIMESTAMP, 
created_by		INT					NOT NULL,
updated_at		TIMESTAMP											ON UPDATE CURRENT_TIMESTAMP,
updated_by		INT,
PRIMARY KEY (announcement_id),
INDEX idx_user_id (created_by),
INDEX dept_id (dept_id),
FOREIGN KEY (created_by) REFERENCES User (user_id),
FOREIGN KEY (dept_id) REFERENCES Department (dept_id)
);

CREATE TABLE AuditLog (
log_id			INT					NOT NULL  						AUTO_INCREMENT,
user_id			INT					NOT NULL, 
action			ENUM('Create', 'Update', 'Delete', 'Archive')		NOT NULL, 
entity_type		VARCHAR(50)			NOT NULL, 
entity_id		INT					NOT NULL, 
before_json		JSON, 
after_json		JSON, 
diff_json		JSON,
role_id			INT,
occurred_at		TIMESTAMP			NOT NULL, 
PRIMARY KEY (log_id),
INDEX user_id (user_id),
INDEX role_id (role_id),
INDEX entity_id (entity_id),
INDEX idx_occurred_at (occurred_at),
FOREIGN KEY (role_id) REFERENCES Role (role_id),
FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE TABLE VisitorRequest (
request_id		INT					NOT NULL  						AUTO_INCREMENT,
full_name		VARCHAR(50)			NOT NULL, 
contact_email	VARCHAR(50)			NOT NULL, 
visitor_msg		TEXT				NOT NULL, 
msg_status		ENUM('Pending', 'Reviewed', 'Finalized')	NOT NULL DEFAULT 'Pending', 
session_id		VARCHAR(50)			NOT NULL, 
created_at      TIMESTAMP       	NOT NULL 			DEFAULT CURRENT_TIMESTAMP, 
PRIMARY KEY (request_id)
);

CREATE TABLE Session (
  session_id       CHAR(64)        NOT NULL,
  user_id          INT             NOT NULL,
  created_at       TIMESTAMP       NOT NULL 			DEFAULT CURRENT_TIMESTAMP,
  last_seen_at     TIMESTAMP       NOT NULL 			DEFAULT CURRENT_TIMESTAMP,
  expires_at       TIMESTAMP       NOT NULL,
  revoked_at       TIMESTAMP       NULL,
  PRIMARY KEY (session_id),
  INDEX (user_id),
  INDEX (expires_at),
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

CREATE TABLE MemberSuggestion (
	suggestion_id 	INT 				AUTO_INCREMENT 			PRIMARY KEY,
    user_id 		INT 				NOT NULL,
    suggestion_text TEXT 				NOT NULL,
    attachment_path VARCHAR(255),
    status 			ENUM('Pending','Reviewed','Resolved') 		DEFAULT 'Pending',
    created_at 		TIMESTAMP 			DEFAULT CURRENT_TIMESTAMP,
	resolved_by INT,
	FOREIGN KEY (user_id) REFERENCES User(user_id)
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
last_login, 
last_updated,
updated_by
)

VALUES

(1, 'Zendaya', 'Coleman', 'zend0009@email.com', '(217) 555-0101', '101 Maple Grove Drive Springfield, IL 62704', '$2y$10$kw5f0o4KGqtRwysU8o.Qn.HJIKgDfkctvyII75mYbsOKrJ9VFc7Rm', 1, '2022-03-15 08:12:45', '2023-07-21 14:55:12', NULL, NULL), 
(2, 'Scarlett', 'Johansson', 'scj11390@email.com', '(608) 555-0102', '245 Oak Valley Road Madison, WI 53711', '$2y$10$5LJs/YsrRj66QCORnDyNsOK.58J7p95UAc.FqnjVRccjgPlpl3pHy', 1, '2021-11-02 16:45:30', '2023-01-18 09:23:50', NULL, NULL),
(3, 'Joyce', 'Byers', 'byers506@email.com', '(919) 555-0103', '389 Pine Hill Lane Raleigh, NC 27607', '$2y$10$kBqOL5e1wDheS8DHTBl6bOKkpzjXM2hRWDqNM.KWjr/wifbhFYQni', 1, '2020-06-10 12:05:10', '2022-12-22 18:40:05', NULL, NULL),
(4, 'Ariana', 'Grande', 'ari03993@email.com', '(626) 555-0104', '512 Sunset Ridge Avenue Pasadena, CA 91105', '$2y$10$ps9ws6TzOeIV6pBZZDeEZOOBQnYIvDtDIsfE5cj.V6LNaPtH16.pS', 1, '2023-01-22 10:20:00', '2023-12-15 11:05:45', NULL, NULL), 
(5, 'Taylor', 'Swift', 'swift133@email.com', '(802) 555-0105', '76 Riverstone Court Burlington, VT 05401', '$2y$10$0d4cPeCkCZhybZ6S1Dc.Q.gjAShuancxpzwDoWJkvch4znII/ftCi', 1, '2021-08-05 14:30:25', '2023-04-03 07:50:12' , NULL, NULL),
(6, 'Sabrina', 'Carpenter', 'sabcarp8@email.com', '(972) 555-0106', '834 Willow Bend Way Plano, TX 75024', '$2y$10$QjUoa70XLel3.7dMehpBAOw.eazg.8udTQh8WVGP1336K.w70ANRG', 1, '2022-02-28 09:12:15', '2023-08-09 21:15:35', NULL, NULL),
(7, 'Hailee', 'Steinfeld', 'hai47073@email.com', '(425) 555-0107', '690 Cedar Ridge Drive Bellevue, WA 98008', '$2y$10$S.8/Vr3.yfNkG3QApZoO1e.GtM7nfRJRalqGhTJAkeLKjdqZ1o1I6', 1, '2020-12-15 22:10:50', '2022-10-30 08:05:25', NULL, NULL),
(8, 'Sadie', 'Sink', 'sink9292@email.com', '(614) 555-0108', '918 Meadowbrook Lane Columbus, OH 43221', '$2y$10$YlawsO6QyO5qO0ezFK6H6OTkulwtUYsj8hJxSApG.S4gNyTsG.7Je', 1, '2021-05-19 07:45:40', '2023-03-12 19:35:50', NULL, NULL), 
(9, 'Margot', 'Robbie', 'robbie01@email.com', '(847) 555-0109', '207 Lakeside Parkway Evanston, IL 60202', '$2y$10$LLmgwqQ9NDqQKVu/ax3oaOgfmbSuQ62kg0Quuvxs46PCMPILGYPXq', 1, '2023-02-10 13:15:30', '2023-12-01 16:40:20', NULL, NULL),
(10, 'Olivia', 'Rodrigo', 'oliv2005@email.com', '(480) 555-0110', '561 Highland Park Drive Scottsdale, AZ 85255', '$2y$10$mUr9yZwVvkUoUwlqzmP4ieKXGH8b1kHqmsovCFo2UXzYcHR1/LHvq', 1, '2021-09-30 11:25:55', '2023-09-18 10:15:00', NULL, NULL), 
(11, 'Simone', 'Biles', 'sbiles12@email.com', '(919) 555-0111', '744 Brookhaven Road Chapel Hill, NC 27516', '$2y$10$IZfz/u/54SqWzcb0otAq2eQb5uwOJm/X0jmJhrD3I21AlXnO3d3Bq', 1, '2022-07-07 17:40:20', '2023-06-11 22:55:10', NULL, NULL), 
(12, 'Winona', 'Ryder', 'ryd80189@email.com', '(707) 555-0112', '1205 Redwood Springs Circle Santa Rosa, CA 95404', '$2y$10$PmNOQP2FrN50jE4ghiWCcuqI.FBsqto1syRl9TblNRZMtu6q6Ouz2', 1, '2020-03-22 06:55:10', '2022-11-08 13:10:25', NULL, NULL), 
(13, 'Kylie', 'Jenner', 'jennnr24@email.com', '(828) 555-0113', '967 Autumn Crest Lane Asheville, NC 28803', '$2y$10$tmhVscmPDY.eymVx1fAcbepfGLyI.aEOphCWBQ.vi0Bu4Qb2JnUEi', 1, '2021-12-25 19:05:45', '2023-08-05 17:30:15', NULL, NULL),
(14, 'Lindsay', 'Lohan', 'linloh23@email.com', '(609) 555-0114', '29 Stonegate Boulevard Princeton, NJ 08540', '$2y$10$ncUkQZ.UpLK98sgHSx8iuemii1YVZcK3phdYkRdTh0G6Hs3kowcim', 1, '2021-01-18 15:30:40', '2023-05-25 12:45:35', NULL, NULL), 
(15, 'Zara', 'Larsson', 'lar23342@email.com', '(410) 555-0115', '392 Harbor Point Drive Annapolis, MD 21403', '$2y$10$1Ck3Dmd3lE7drK/MIomR0.WEl9Wzo6aj2ec0vP5PBwWPwVAJiYqlm', 1, '2022-09-05 09:15:25', '2023-10-29 08:05:55', NULL, NULL), 
(16, 'Gracie', 'Abrams', 'abg41207@email.com', '(410) 555-0116', '32 Lakeview Crescent, Mount Pleasant, SC 29464', '$2y$10$yeNHHp5i25BeyAcXPyQvMuG/l4ib0opBdsff9ES50Dd3PLQF/Ufk2', 1, '2022-09-05 09:40:00', '2022-09-20 14:30:45', NULL, NULL),
(17, 'Zoha', 'K', 'kha27882@email.com', '(406) 555-0117', '155 Golden Meadow Drive Bozeman, MT 59718', '$2y$10$hugC3ImjPgD9yz4Xw.ZqTO0bHkj1P1MkzdREMnH7c/xVF.vCec3L2', 1, '2023-03-12 12:55:30', '2023-12-03 20:25:50', NULL, NULL),
(18, 'JJ', 'G', 'gil42134@email.com', '(941) 555-0118', '880 Cypress Hollow Road Sarasota, FL 34232', '$2y$10$ocq4zYQSUKbqVXvY/GZeWOfBWslM09JPpgXokfiJ8RJqWPPy28Tke', 1, '2021-06-28 18:20:15', '2023-07-15 09:05:40', NULL, NULL),
(19, 'Kah', 'O', 'ong92990@email.com', '(970) 555-0119', '602 Juniper Ridge Lane Fort Collins, CO 80525', '$2y$10$f/2.BUqrR7y7NdK.OSOU1uHZ3puYmPzIAHUaFia4pas9FZCdT4lYG', 1, '2022-04-09 10:10:50', '2023-09-22 15:50:25', NULL, NULL),  
(20, 'Shan', 'K', 'kat44977@email.com', '(859) 555-0120', '2173 Bluebird Crossing Lexington, KY 40503', '$2y$10$/kIYD9Ryl1u.T65I6n.AgeK8wW8i7Q4Ca1v.YPkUdLhwSum6Ip0hK', 1, '2020-11-14 08:35:20', '2022-12-18 11:45:10', NULL, NULL);


INSERT INTO Role 
(
role_id,
role_name,
role_desc
)
VALUES
(1, 'President', 'Oversees the entire organization makes final decisions and ensures overall system and organizational integrity'),
(2, 'Department Head', 'Manages department operations reporting and member activities and serves as the primary liaison to the President'),
(3, 'Member', 'Participates in organizational activities submits required forms and engages with events and communications'),
(4, 'Admin', 'Maintains the technical health security and configuration of the system without participating in organizational decision-making');


INSERT INTO UserRole 
(
user_id,
role_id	
)
VALUE
(1, 1),
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
(17, 1),
(17, 2),
(17, 3),
(17, 4),
(18, 1),
(18, 2),
(18, 3),
(18, 4),
(19, 1),
(19, 2),
(19, 3),
(19, 4),
(20, 1),
(20, 2),
(20, 3),
(20, 4);


INSERT INTO Department 
(
dept_id,
user_id,
role_id,
dept_name,
dept_desc
)
VALUES
(1, 17, 2, 'Reporting Department', 'The Reporting Department is responsible for collecting organizing analyzing and consolidating organizational data into accurate reports');


INSERT INTO CalendarEvent 
(
event_id,
event_title,
event_desc,
event_location,
event_date,
created_at,
created_by
)
VALUES
(1, 'First Day of Class', 'The first day of Spring Term begins', 'D2L', '2026-01-20 00:00:00', '2026-01-08 14:42:45', 20),   
(2, 'Team Meeting', 'Gather to discuss future developments', 'Zoom', '2026-01-23 12:30:00', '2026-01-08 14:48:31', 20), 
(3, 'Iftari', 'Iftari Dinner starting at 5 PM', 'Mosque', '2026-03-07 18:00:00', current_timestamp(), 17),
(4, 'Iftari', 'Iftari Dinner starting at 5 PM', 'Mosque', '2026-03-14 18:00:00', current_timestamp(), 17),
(5, 'Eid', 'Program starts at 10 AM', 'Mosque', '2026-03-20 10:00:00', current_timestamp(), 17),
(6, 'March Meeting', 'Program will start at 11 AM', 'Mosque', '2026-03-29 10:00:00', current_timestamp(), 17),
(7, 'April Meeting', 'Program will start at 10 AM and continue until 2PM', 'Mosque', '2026-04-11 10:00:00', current_timestamp(), 17),
(8, 'Sunday School', 'Classes will start at 12PM', 'Mosque', '2026-04-05 10:00:00', current_timestamp(), 17),
(9, 'Sunday School', 'Classes will start at 12PM', 'Mosque', '2026-04-12 10:00:00', current_timestamp(), 17),
(10, 'Sunday School', 'Classes will start at 12PM', 'Mosque', '2026-04-19 10:00:00', current_timestamp(), 17),
(11, 'Sunday School', 'Classes will start at 12PM', 'Mosque', '2026-04-26 10:00:00', current_timestamp(), 17);

INSERT INTO Permission 
(
permission_id,
perm_title, 
perm_desc, 
perm_resource, 
perm_crud
)
VALUES
(1, 'Create User Account', 'Create a new member account by entering required credentials and assigning an initial active status', 'User', 'Create'),
(2, 'Update User Account Status', 'Freeze or reactivate user accounts immediately revoking or restoring login access while preserving data', 'User', 'Update'), 
(3, 'View User Accounts', 'View all user accounts and related metadata for monitoring and management', 'User', 'Read'),
(4, 'View User Activity Logs', 'View user engagement metrics including login frequency last login time and site activity', 'User', 'Read'),
(5, 'Receive Message Requests', 'Receive and view membership requests submitted by visitors via contact form', 'Suggestion', 'Read'),
(6, 'Respond to Requests', 'Accept or decline visitor membership requests', 'Suggestion', 'Update'),
(7, 'Cancel Calendar Event', 'Cancel any calendar event or recurring series across the organization', 'Calendar Event', 'Update'),
(8, 'Upload Document', 'Upload documents within size and performance constraints', 'Document', 'Create'),
(9, 'Set Document Visibility', 'Define which roles or departments can view and download a document', 'Document', 'Update'),
(10, 'View Documents', 'View and download documents based on assigned visibility permissions', 'Document', 'Read'),
(11, 'Archive Document', 'Archive documents so they are no longer active but retained for historical reference', 'Document', 'Update'),
(12, 'Create Calendar Event', 'Create calendar events that are visible to selected roles within seconds of posting', 'Calendar Event', 'Create'),
(13, 'Update Calendar Event', 'Edit calendar events according to defined modification permissions', 'Calendar Event', 'Update'),
(14, 'Publish Announcement', 'Create and publish announcements with title body and expiry date', 'Announcement', 'Create'),
(15, 'Update Announcement', 'Edit active announcements while expired announcements auto-hide', 'Announcement', 'Update'),
(16, 'View Announcement Archive', 'Archive any announcement across the organization to preserve history without deletion', 'Announcement', 'Update'),
(17, 'View Reports', 'View monthly aggregated reports with visualizations and qualitative interpretations', 'Form Response', 'Read'),
(18, 'Cancel Department Calendar Event', 'Cancel calendar events they created or in their department only', 'Calendar Event', 'Update'),
(19, 'View Own Profile', 'View personal profile information including name contact details role and available navigation options', 'User', 'Read'),
(20, 'Update Own Profile', 'Update personal profile information such as name email phone number and address', 'User', 'Update'),
(21, 'Submit Form Response', 'Complete and submit a survey or form related to member involvement', 'Form Response', 'Create'),
(22, 'Update Own Form Response', 'Edit a previously submitted survey response before the submission deadline to correct errors', 'Form Response', 'Update'),
(23, 'View Own Form Responses', 'View a list of previously submitted form responses and open individual submissions in read-only mode', 'Form Response', 'Read'),
(24, 'View Member Announcements', 'View published announcements intended for members', 'Announcement', 'Read'),
(25, 'Submit Message Suggestions', 'Visitors and all members can send membership requests via contact form', 'Suggestion', 'Create'),
(26, 'Reset Password', 'Reset account password using a time-limited tokenized email link with enforced password strength rules', 'User', 'Update'),
(27, 'View Calendar Events', 'View all calendar events across roles with consistent formatting', 'Calendar Event', 'Read'), 
(28, 'View Announcements', 'View announcements and their current status', 'Announcement', 'Read'), 
(29, 'View System Logs', 'View system log entries for monitoring and troubleshooting', 'Audit Log', 'Read'),
(30, 'Export System Logs', 'Generate and export system logs as files for auditing and archival purposes', 'Audit Log', 'Read'),
(31, 'Assign User Roles', 'Assign or change user roles with immediate effect and audit logging of changes', 'User Role', 'Update'),
(32, 'Grant Role Permissions', 'Grant or modify permissions associated with roles to control system access', 'Role Permission', 'Update'),
(33, 'View Performance Metrics', 'View system performance metrics and operational data through an administrative dashboard', 'Audit Log', 'Read'),
(34, 'View System Alerts', 'View system alerts and notifications related to failures or downtime within the past 90 days', 'Audit Log', 'Read');


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


INSERT INTO FormTemplate (
template_id,
temp_title, 
temp_desc, 
temp_status, 
form_questions, 
form_deadline
)

VALUES 
(1, "Monthly Members Survey", 
	"This form is anonymous. All questions should be answered from your own induvidual experiences. Group activity should be reported to your Local Shoba or Department Secretary.", 
	"Active", 

JSON_ARRAY(
	JSON_OBJECT( 
		'id', 1,
		'question', 'Majis',
		'options', JSON_ARRAY('PA-York/Harrisburg', 'PA-Pittsburgh', 'IT-Testing')
		),
	JSON_OBJECT(
		'id', 2,
		'question', 'Reporting Month',
		'options', JSON_ARRAY(CONCAT(MONTHNAME(CURRENT_DATE), ' ', YEAR(CURRENT_DATE)))
		),
	JSON_OBJECT(
		'id', 3,
		'question', 'Is this your first time completing this survey?',
		'options', JSON_ARRAY('Yes', 'No')
		),
	JSON_OBJECT(
		'id', 4,
		'question', 'Are you a NAU MUBAI''A',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', 'NAU MUBAI''A: Accepted Islam Ahmadiyyat in last three years'
		),
	JSON_OBJECT(
		'id', 5,
		'question', 'Which auxiliary are you from',
		'options', JSON_ARRAY('Lajna', 'Nasirat')
		),
		/*
			IF Nasirat SELECTED 
		*/
	JSON_OBJECT(
		'id', 6,
		'question', 'What age group are you in?',
		'options', JSON_ARRAY('7-9', '10-12', '13-15'),
		'branch', 0
		),
	JSON_OBJECT(
		'id', 7,
		'question', 'Are you regular in offering your Salat?',
		'options', JSON_ARRAY('I offer five daily prayers', 'I offer three daily prayers', 'In progress/working on it', 'No, I don''t offer Salat'),
		'branch', 0
		),
	JSON_OBJECT(
		'id', 8,
		'question', 'Do you know the translation of Salat?',
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 0
		),
	JSON_OBJECT(
		'id', 9,
		'question', 'Do you recite the Holy Qur''an daily?',
		'options', JSON_ARRAY('Yes', 'No', 'Working on it'),
		'context', '(Try to recite in the morning before school. If that''s not possible, recite after school.)',
		'branch', 0
		),
	JSON_OBJECT(
		'id', 10,
		'question', 'Do you watch/read the Friday Sermon?',
		'options', JSON_ARRAY('Once a month', 'Twice a month', 'Every week (all of them)', 'I don''t watch/read the Friday Sermon'),
		'context', '(You should strive to watch the sermon live. If unable, please watch it later and read the sermon to gain full understanding.)',
		'branch', 0
		),
	JSON_OBJECT(
		'id', 11,
		'question', 'Do you regularly watch MTA?',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', '(This could be a couple times a week or month, does not have to be daily)',
		'branch', 0
		),
	JSON_OBJECT(
		'id', 12,
		'question', 'Do you regularly look over the Nasirat Syllabus and are up to date with it?',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', '(This includes attending weekly or monthly classes and following the guidance of your Nasirat Secretary.)',
		'branch', 0
		),
	JSON_OBJECT(
		'id', 13,
		'question', 'Do you regularly exercise?',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', '(This can include sports, physical education classes, walks, or other physical activity—even if it is once a month.)',
		'branch', 0
		),
		/*
			IF Lajna SELECTED 
		*/

	/* Office Bearer */
	JSON_OBJECT(
		'id', 14,
		'question', 'Are you a member of Local Amila?',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 1
		),
	JSON_OBJECT(
		'id', 15,
		'question', 'Do you make prayer at Mosque or Salat Center?',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 1
		),
	JSON_OBJECT(
		'id', 16,
		'question', 'How often do you make prayer at Local Mosque or Salat Center?',
		'options', JSON_ARRAY('Five daily', 'Three daily', 'Juma only', 'Other'),
		'branch', 1,
		'group', 1
		),	

	/* Taleem (Education) */
	JSON_OBJECT(
		'id', 17,
		'question', 'Knowledge of Salat: ',
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 18,
		'question', 'Knowledge of Salat with translation: ',
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 19, 
		'question', 'Reciting Holy Qur''an with basic correct pronunciation:', 
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 20,
		'question', 'Knowledge of Holy Quran with translation: ',
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 21,
		'question', 'Qur''an class attendance (pronunciation): ',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', 'i.e. attend Al Furqan or Tahir Academy HQ',
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 22,
		'question', 'Qur''an class attendance (translation): ',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 23,
		'question', 'Following the Taleem Syllabus: ',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 2
		),
	JSON_OBJECT(
		'id', 24,
		'question', 'Studying/Reading Jama''at books or Lajna Publications: ',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', 'Lajna/Nasirat assigned',
		'branch', 1,
		'group', 2
		),
	
	/* Lajna/Nasirat books: */
	JSON_OBJECT(
		'id', 25,
		'question', 'Which book(s) were completed?',
		'branch', 1,
		'group', 3
		),

	/* Tarbiyyat (Moral Training) */
	JSON_OBJECT(
		'id', 26,
		'question', 'Recite the Holy Qur''an daily:',
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 27,
		'question', 'Reciting the Holy Qur''an with translation:',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 28,
		'question', 'Regular in offering Salat: ',
		'options', JSON_ARRAY('Offer 5 daily salat', 'Offer 3 daily salat', 'Offer occassional salat', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 29,
		'question', 'Offer Salat in congregation',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 30,
		'question', 'Following the Tarbiyyat Syllabus: ',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 31,
		'question', 'Listening/Reading the Friday Sermon: ',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 32,
		'question', 'Regular watching MTA: ',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 33,
		'question', 'Observing Purdah: ',
		'options', JSON_ARRAY('Yes', 'No', 'In progress/working on it'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 34,
		'question', 'Regularly exercising',
		'options', JSON_ARRAY('Yes', 'No'),
		'branch', 1,
		'group', 4
		),
	JSON_OBJECT(
		'id', 35,
		'question', 'Did you do any community service?',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', 'Inside or outside Jamaat?',
		'branch', 1,
		'group', 4
		),
		
	/* Tabligh (Preaching) */
	JSON_OBJECT(
		'id', 36,
		'question', 'Did you preach this month?',
		'options', JSON_ARRAY('Yes', 'No'),
		'context', 'Dai''yat: Member who is actively preaching'	,
		'branch', 1,
		'group', 5
		)
	
	), 
	CONCAT(YEAR(CURRENT_DATE), '-',LPAD(MONTH(CURRENT_DATE),2,'0'), '-10 ', '23:59:59')
);



INSERT INTO FormResponse (
response_id,
template_id,
user_id,
form_response,
form_status
)

VALUES
(1, 1, 5, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Lajna"},
{"id": 14, "response": "Yes"},
{"id": 15, "response": "Yes"},
{"id": 16, "response": "Five daily"},
{"id": 17, "response": "Yes"},
{"id": 18, "response": "Yes"},
{"id": 19, "response": "In progress/working on it"},
{"id": 20, "response": "Yes"},
{"id": 21, "response": "Yes"},
{"id": 22, "response": "No"},
{"id": 23, "response": "Yes"},
{"id": 24, "response": "No"},
{"id": 25, "response": "None"},
{"id": 26, "response": "Yes"},
{"id": 27, "response": "No"},
{"id": 28, "response": "Offer 5 daily salat"},
{"id": 29, "response": "Yes"},
{"id": 30, "response": "Yes"},
{"id": 31, "response": "Yes"},
{"id": 32, "response": "Yes"},
{"id": 33, "response": "Yes"},
{"id": 34, "response": "Yes"},
{"id": 35, "response": "No"},
{"id": 36, "response": "Yes"}
]', "Pending"),		

(2, 1, 6, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Lajna"},
{"id": 14, "response": "Yes"},
{"id": 15, "response": "Yes"},
{"id": 16, "response": "Five daily"},
{"id": 17, "response": "Yes"},
{"id": 18, "response": "Yes"},
{"id": 19, "response": "Yes"},
{"id": 20, "response": "Yes"},
{"id": 21, "response": "Yes"},
{"id": 22, "response": "Yes"},
{"id": 23, "response": "Yes"},
{"id": 24, "response": "Yes"},
{"id": 25, "response": "book"},
{"id": 26, "response": "Yes"},
{"id": 27, "response": "No"},
{"id": 28, "response": "Offer 5 daily salat"},
{"id": 29, "response": "Yes"},
{"id": 30, "response": "Yes"},
{"id": 31, "response": "Yes"},
{"id": 32, "response": "Yes"},
{"id": 33, "response": "Yes"},
{"id": 34, "response": "Yes"},
{"id": 35, "response": "Yes"},
{"id": 36, "response": "Yes"}
]', "Pending"),		

(3, 1, 7, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "No"},
{"id": 5, "response": "Lajna"},
{"id": 14, "response": "Yes"},
{"id": 15, "response": "Yes"},
{"id": 16, "response": "Juma only"},
{"id": 17, "response": "In progress/working on it"},
{"id": 18, "response": "Yes"},
{"id": 19, "response": "Yes"},
{"id": 20, "response": "In progress/working on it"},
{"id": 21, "response": "Yes"},
{"id": 22, "response": "Yes"},
{"id": 23, "response": "Yes"},
{"id": 24, "response": "Yes"},
{"id": 25, "response": "book"},
{"id": 26, "response": "In progress/working on it"},
{"id": 27, "response": "Yes"},
{"id": 28, "response": "Offer 5 daily salat"},
{"id": 29, "response": "Yes"},
{"id": 30, "response": "Yes"},
{"id": 31, "response": "Yes"},
{"id": 32, "response": "No"},
{"id": 33, "response": "Yes"},
{"id": 34, "response": "Yes"},
{"id": 35, "response": "Yes"},
{"id": 36, "response": "No"}
]', "Pending"),	
		
(4, 1, 8, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "No"},
{"id": 5, "response": "Lajna"},
{"id": 14, "response": "No"},
{"id": 15, "response": "Yes"},
{"id": 16, "response": "Juma only"},
{"id": 17, "response": "Yes"},
{"id": 18, "response": "Yes"},
{"id": 19, "response": "Yes"},
{"id": 20, "response": "In progress/working on it"},
{"id": 21, "response": "Yes"},
{"id": 22, "response": "No"},
{"id": 23, "response": "Yes"},
{"id": 24, "response": "Yes"},
{"id": 25, "response": "book"},
{"id": 26, "response": "No"},
{"id": 27, "response": "Yes"},
{"id": 28, "response": "Offer 3 daily salat"},
{"id": 29, "response": "Yes"},
{"id": 30, "response": "Yes"},
{"id": 31, "response": "No"},
{"id": 32, "response": "No"},
{"id": 33, "response": "Yes"},
{"id": 34, "response": "No"},
{"id": 35, "response": "No"},
{"id": 36, "response": "No"}
]', "Pending"),

(5, 1, 9, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "Yes"},
{"id": 4, "response": "No"},
{"id": 5, "response": "Lajna"},
{"id": 14, "response": "No"},
{"id": 15, "response": "No"},
{"id": 16, "response": "Three daily"},
{"id": 17, "response": "Yes"},
{"id": 18, "response": "Yes"},
{"id": 19, "response": "Yes"},
{"id": 20, "response": "Yes"},
{"id": 21, "response": "No"},
{"id": 22, "response": "No"},
{"id": 23, "response": "Yes"},
{"id": 24, "response": "Yes"},
{"id": 25, "response": "book"},
{"id": 26, "response": "In progress/working on it"},
{"id": 27, "response": "Yes"},
{"id": 28, "response": "Offer occassional salat"},
{"id": 29, "response": "No"},
{"id": 30, "response": "Yes"},
{"id": 31, "response": "Yes"},
{"id": 32, "response": "No"},
{"id": 33, "response": "In progress/working on it"},
{"id": 34, "response": "Yes"},
{"id": 35, "response": "Yes"},
{"id": 36, "response": "No"}
]', "Pending"),

(6, 1, 10, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Lajna"},
{"id": 14, "response": "No"},
{"id": 15, "response": "Yes"},
{"id": 16, "response": "Juma only"},
{"id": 17, "response": "Yes"},
{"id": 18, "response": "Yes"},
{"id": 19, "response": "Yes"},
{"id": 20, "response": "Yes"},
{"id": 21, "response": "No"},
{"id": 22, "response": "No"},
{"id": 23, "response": "Yes"},
{"id": 24, "response": "Yes"},
{"id": 25, "response": "book"},
{"id": 26, "response": "In progress/working on it"},
{"id": 27, "response": "Yes"},
{"id": 28, "response": "Offer occassional salat"},
{"id": 29, "response": "No"},
{"id": 30, "response": "Yes"},
{"id": 31, "response": "Yes"},
{"id": 32, "response": "No"},
{"id": 33, "response": "Yes"},
{"id": 34, "response": "No"},
{"id": 35, "response": "Yes"},
{"id": 36, "response": "No"}
]', "Pending"),
	
(7, 1, 11, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "No"},
{"id": 5, "response": "Nasirat"},
{"id": 6, "response": "13-15"},
{"id": 7, "response": "I offer five daily prayers"},
{"id": 8, "response": "Yes"},
{"id": 9, "response": "Yes"},
{"id": 10, "response": "Once a month"},
{"id": 11, "response": "No"},
{"id": 12, "response": "Yes"},
{"id": 13, "response": "Yes"}
]', "Pending"),

(8, 1, 12, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Nasirat"},
{"id": 6, "response": "10-12"},
{"id": 7, "response": "I offer three daily prayers"},
{"id": 8, "response": "Yes"},
{"id": 9, "response": "Yes"},
{"id": 10, "response": "Twice a month"},
{"id": 11, "response": "Yes"},
{"id": 12, "response": "Yes"},
{"id": 13, "response": "No"}
]', "Pending"),

(9, 1, 13, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Nasirat"},
{"id": 6, "response": "10-12"},
{"id": 7, "response": "I offer five daily prayers"},
{"id": 8, "response": "Yes"},
{"id": 9, "response": "Working on it"},
{"id": 10, "response": "Every week (all of them)"},
{"id": 11, "response": "Yes"},
{"id": 12, "response": "Yes"},
{"id": 13, "response": "Yes"}
]', "Pending"),

(10, 1, 14, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "Yes"},
{"id": 4, "response": "No"},
{"id": 5, "response": "Nasirat"},
{"id": 6, "response": "7-9"},
{"id": 7, "response": "In progress/working on it"},
{"id": 8, "response": "No"},
{"id": 9, "response": "Working on it"},
{"id": 10, "response": "Once a month"},
{"id": 11, "response": "Yes"},
{"id": 12, "response": "No"},
{"id": 13, "response": "Yes"}
]', "Pending"),

(11, 1, 15, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Nasirat"},
{"id": 6, "response": "13-15"},
{"id": 7, "response": "I offer five daily prayers"},
{"id": 8, "response": "Yes"},
{"id": 9, "response": "Working on it"},
{"id": 10, "response": "Twice a month"},
{"id": 11, "response": "Yes"},
{"id": 12, "response": "Yes"},
{"id": 13, "response": "Yes"}
]', "Pending"),

(12, 1, 16, '[
{"id": 1, "response": "PA-Pittsburgh"},
{"id": 2, "response": "March 10, 2026"},
{"id": 3, "response": "No"},
{"id": 4, "response": "Yes"},
{"id": 5, "response": "Nasirat"},
{"id": 6, "response": "10-12"},
{"id": 7, "response": "I offer three daily prayers"},
{"id": 8, "response": "In progress/working on it"},
{"id": 9, "response": "Working on it"},
{"id": 10, "response": "Twice a month"},
{"id": 11, "response": "Yes"},
{"id": 12, "response": "Yes"},
{"id": 13, "response": "No"}
]', "Pending");


INSERT INTO FormTemplate (
template_id,
temp_title,
temp_desc,
temp_status,
form_questions,
form_deadline
)

VALUES (
	2,
"Compiled Monthly Report",
"This form is completed by Department Heads to compile monthly departmental activities for the President's review.",
"Active",

JSON_ARRAY(

JSON_OBJECT(
'id', 1,
'question', 'Was a Lajna General Meeting held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 2,
'question', 'Was a Halqa Meeting held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 3,
'question', 'Was an Amila Meeting held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 4,
'question', 'Was a Jamaat Meeting held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 5,
'question', 'Was a Book Club held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 6,
'question', 'Was the monthly Tarteel Rule Covered?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 7,
'question', 'Were the Taleem Syllabus questions answered?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 8,
'question', 'Were any workshops held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 9,
'question', 'Were any presentations held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 10,
'question', 'Was a Seeratul-Nabi Jalsa held by Lajna?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 11,
'question', 'Were the current Friday sermons covered in the Lajna meeting using quiz questions?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 12,
'question', 'Was the workbook prayer reviewed in the Lajna meeting?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 13,
'question', 'Were efforts made to promote observance of Purdah?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 14,
'question', 'Were efforts made to eradicate unIslamic practices?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 15,
'question', 'Were efforts made to inculcate Islamic Morals?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 16,
'question', 'Were efforts made for Rishta Nata?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 17,
'question', 'Was Al-Wassiyat read and discussed in the meeting?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 18,
'question', 'Were Jamaat youth sessions held?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 19,
'question', 'Were Jamaat parent sessions held?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 20,
'question', 'Was the Tabligh syllabus followed for the Quarter?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 21,
'question', 'Were any new Bai’at achieved by Lajna efforts this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 22,
'question', 'Were female public officials presented with the Lajna brochure?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 23,
'question', 'Were distorted images of Islam in classrooms identified?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 24,
'question', 'Was the Khidmat-e-khalq syllabus followed for the Quarter?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 25,
'question', 'Any new convert or newly migrated sister served?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 26,
'question', 'Was Humanity First served?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 27,
'question', 'Was a Title 1 school served?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 28,
'question', 'Do you have Nau Mubaiyat (New Converts)?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 29,
'question', 'Were activities arranged for new sisters this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 30,
'question', 'Was a health seminar or sports day held this month?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 31,
'question', 'Was a Sports Tournament held?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 32,
'question', 'Did your Majlis recycle during Lajna events?',
'options', JSON_ARRAY('Yes','No')
),

JSON_OBJECT(
'id', 33,
'question', 'Was a Umoore Talibat outing organized?',
'options', JSON_ARRAY('Yes','No')
)

),

CONCAT(YEAR(CURRENT_DATE), '-',LPAD(MONTH(CURRENT_DATE),2,'0'), '-25 23:59:59')
);

INSERT INTO FormResponse (response_id, template_id, user_id, form_response) VALUES

(13, 2,1,'[
{"id":1,"response":"Yes"},{"id":2,"response":"Yes"},{"id":3,"response":"Yes"},{"id":4,"response":"No"},{"id":5,"response":"Yes"},
{"id":6,"response":"Yes"},{"id":7,"response":"Yes"},{"id":8,"response":"No"},{"id":9,"response":"Yes"},{"id":10,"response":"Yes"},
{"id":11,"response":"Yes"},{"id":12,"response":"Yes"},{"id":13,"response":"Yes"},{"id":14,"response":"No"},{"id":15,"response":"Yes"},
{"id":16,"response":"Yes"},{"id":17,"response":"Yes"},{"id":18,"response":"Yes"},{"id":19,"response":"No"},{"id":20,"response":"Yes"},
{"id":21,"response":"No"},{"id":22,"response":"Yes"},{"id":23,"response":"Yes"},{"id":24,"response":"Yes"},{"id":25,"response":"No"},
{"id":26,"response":"Yes"},{"id":27,"response":"Yes"},{"id":28,"response":"No"},{"id":29,"response":"Yes"},{"id":30,"response":"Yes"},
{"id":31,"response":"No"},{"id":32,"response":"Yes"},{"id":33,"response":"Yes"}
]'),

(14, 2,2,'[
{"id":1,"response":"Yes"},{"id":2,"response":"No"},{"id":3,"response":"Yes"},{"id":4,"response":"Yes"},{"id":5,"response":"No"},
{"id":6,"response":"Yes"},{"id":7,"response":"Yes"},{"id":8,"response":"Yes"},{"id":9,"response":"No"},{"id":10,"response":"Yes"},
{"id":11,"response":"Yes"},{"id":12,"response":"No"},{"id":13,"response":"Yes"},{"id":14,"response":"Yes"},{"id":15,"response":"Yes"},
{"id":16,"response":"No"},{"id":17,"response":"Yes"},{"id":18,"response":"Yes"},{"id":19,"response":"Yes"},{"id":20,"response":"Yes"},
{"id":21,"response":"No"},{"id":22,"response":"Yes"},{"id":23,"response":"No"},{"id":24,"response":"Yes"},{"id":25,"response":"Yes"},
{"id":26,"response":"Yes"},{"id":27,"response":"No"},{"id":28,"response":"Yes"},{"id":29,"response":"Yes"},{"id":30,"response":"Yes"},
{"id":31,"response":"No"},{"id":32,"response":"Yes"},{"id":33,"response":"No"}
]');


/************************************ 

PROCEDURE FOR AUDIT LOG TABLE UPDATES 

************************************/
DELIMITER $$
CREATE OR REPLACE PROCEDURE generate_updated_json(
     IN tbl_name VARCHAR(64),
	 IN pk_col VARCHAR(64),
     IN old_row JSON,
     IN new_row JSON,
     OUT updated_json JSON
)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE col_name VARCHAR(64);
    DECLARE cols CURSOR FOR
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = tbl_name AND COLUMN_NAME NOT IN (pk_col,'user_id');
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    SET updated_json = JSON_OBJECT();

    OPEN cols;
    read_loop: LOOP
        FETCH cols INTO col_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

    IF NOT (
            JSON_UNQUOTE(JSON_EXTRACT(old_row, CONCAT('$.', col_name)))
            <=>
            JSON_UNQUOTE(JSON_EXTRACT(new_row, CONCAT('$.', col_name)))
        )
        THEN
            SET updated_json = JSON_MERGE_PATCH(
                updated_json,
                JSON_OBJECT(
                    col_name,
                    JSON_UNQUOTE(JSON_EXTRACT(new_row, CONCAT('$.', col_name)))
                )
            );
        END IF;
    END LOOP;
    CLOSE cols;
END$$
DELIMITER ;


/* UPDATE TRIGGER FOR USER TABLE */
DELIMITER $$
CREATE OR REPLACE TRIGGER user_after_update
AFTER UPDATE ON `User`
FOR EACH ROW
BEGIN
    DECLARE changes JSON;
	DECLARE old_row JSON;
    DECLARE new_row JSON;

    SET old_row = JSON_OBJECT(
            'first_name', OLD.first_name,
            'last_name', OLD.last_name,
			'user_email', OLD.user_email,
            'user_phone', OLD.user_phone,
			'user_address', OLD.user_address,
            'is_active', OLD.is_active,
			'last_updated', OLD.last_updated,
			'updated_by', OLD.updated_by
        );
    SET new_row =  JSON_OBJECT(
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
			'user_email', NEW.user_email,
            'user_phone', NEW.user_phone,
			'user_address', NEW.user_address,
            'is_active', NEW.is_active,
			'last_updated', NEW.last_updated,
			'updated_by', NEW.updated_by
        );

    CALL generate_updated_json('User', 'user_id', old_row, new_row, changes);

    IF JSON_LENGTH(changes) > 0 THEN
        INSERT INTO AuditLog (
            user_id,
			role_id,
			action,
			entity_type,
			entity_id,
			before_json,
			after_json,
			diff_json
        )
        VALUES (
            NEW.updated_by,
			@current_role_id,
			'UPDATE',
			'User',
            NEW.user_id,
            old_row,
		    new_row,
        	changes
        );
    END IF;
END$$
DELIMITER ;

/* UPDATE TRIGGER FOR CALENDAR EVENT TABLE */
DELIMITER $$
CREATE OR REPLACE  TRIGGER event_after_update
AFTER UPDATE ON `CalendarEvent`
FOR EACH ROW
BEGIN
    DECLARE changes JSON;
	DECLARE old_row JSON;
    DECLARE new_row JSON;

    SET old_row = JSON_OBJECT(
            'event_title', OLD.event_title,
            'event_desc', OLD.event_desc,
			'event_location', OLD.event_location,
            'event_date', OLD.event_date,
			'updated_at', OLD.updated_at,
			'updated_by', OLD.updated_by
        );
    SET new_row =  JSON_OBJECT(
            'event_title', NEW.event_title,
            'event_desc', NEW.event_desc,
			'event_location', NEW.event_location,
            'event_date', NEW.event_date,
			'updated_at', NEW.updated_at,
			'updated_by', NEW.updated_by
        );

    CALL generate_updated_json('CalendarEvent', 'event_id', old_row, new_row, changes);

    IF JSON_LENGTH(changes) > 0 THEN
        INSERT INTO AuditLog (
            user_id,
			role_id,
			action,
			entity_type,
			entity_id,
			before_json,
			after_json,
			diff_json
        )
        VALUES (
            @current_user_id,
			@current_role_id,
			'UPDATE',
			'CalendarEvent',
            NEW.event_id,
            old_row,
		    new_row,
        	changes
        );
    END IF;
END$$
DELIMITER ;

/* UPDATE TRIGGER FOR ANNOUNCEMENT TABLE */
DELIMITER $$
CREATE OR REPLACE  TRIGGER announcement_after_update
AFTER UPDATE ON `Announcement`
FOR EACH ROW
BEGIN
    DECLARE changes JSON;
	DECLARE old_row JSON;
    DECLARE new_row JSON;

    SET old_row = JSON_OBJECT(
            'visibility_scope', OLD.visibility_scope,
            'announce_title', OLD.announce_title,
			'announce_body', OLD.announce_body,
            'announce_expiry', OLD.announce_expiry,
			'allow_opt_out', OLD.allow_opt_out,
			'announce_delivery', OLD.announce_delivery,
            'archived', OLD.archived,
			'updated_at', OLD.updated_at,
			'updated_by', OLD.updated_by
        );
    SET new_row =  JSON_OBJECT(
            'visibility_scope', NEW.visibility_scope,
            'announce_title', NEW.announce_title,
			'announce_body', NEW.announce_body,
            'announce_expiry', NEW.announce_expiry,
			'allow_opt_out', NEW.allow_opt_out,
			'announce_delivery', NEW.announce_delivery,
            'archived', NEW.archived,
			'updated_at', NEW.updated_at,
			'updated_by', NEW.updated_by
        );

    CALL generate_updated_json('Announcement', 'announcement_id', old_row, new_row, changes);

    IF JSON_LENGTH(changes) > 0 THEN
        INSERT INTO AuditLog (
            user_id,
			role_id,
			action,
			entity_type,
			entity_id,
			before_json,
			after_json,
			diff_json
        )
        VALUES (
            @current_user_id,
			@current_role_id,
			'UPDATE',
			'Announcement',
            NEW.announcement_id,
            old_row,
		    new_row,
        	changes
        );
    END IF;
END$$
DELIMITER ;


/* UPDATE TRIGGER FOR DOCUMENT TABLE */
DELIMITER $$
CREATE OR REPLACE  TRIGGER document_after_update
AFTER UPDATE ON `Document`
FOR EACH ROW
BEGIN
    DECLARE changes JSON;
	DECLARE old_row JSON;
    DECLARE new_row JSON;

    SET old_row = JSON_OBJECT(
            'visibility_scope', OLD.visibility_scope,
            'doc_title', OLD.doc_title,
			'stored_url', OLD.stored_url,
            'archived', OLD.archived,
			'updated_at', OLD.updated_at,
			'updated_by', OLD.updated_by
        );
    SET new_row =  JSON_OBJECT(
            'visibility_scope', NEW.visibility_scope,
            'doc_title', NEW.doc_title,
			'stored_url', NEW.stored_url,
            'archived', NEW.archived,
			'updated_at', NEW.updated_at,
			'updated_by', NEW.updated_by
        );

    CALL generate_updated_json('Document', 'document_id', old_row, new_row, changes);

    IF JSON_LENGTH(changes) > 0 THEN
        INSERT INTO AuditLog (
            user_id,
			role_id,
			action,
			entity_type,
			entity_id,
			before_json,
			after_json,
			diff_json
        )
        VALUES (
            NEW.updated_by,
			@current_role_id,
			'UPDATE',
			'Document',
            NEW.document_id,
            old_row,
		    new_row,
        	changes
        );
    END IF;
END$$
DELIMITER ;


/* UPDATE TRIGGER FOR ATTENDANCE TABLE */
DELIMITER $$
CREATE OR REPLACE  TRIGGER attendance_after_update
AFTER UPDATE ON `Attendance`
FOR EACH ROW
BEGIN
    DECLARE changes JSON;
	DECLARE old_row JSON;
    DECLARE new_row JSON;

    SET old_row = JSON_OBJECT(
            'user_id', OLD.user_id,
            'event_id', OLD.event_id,
			'attend_status', OLD.attend_status,
            'check_in_time', OLD.check_in_time,
			'notes', OLD.notes
        );
	 SET new_row = JSON_OBJECT(
            'user_id', NEW.user_id,
            'event_id', NEW.event_id,
			'attend_status', NEW.attend_status,
            'check_in_time', NEW.check_in_time,
			'notes', NEW.notes
        );

    CALL generate_updated_json('Attendance', 'attendance_id', old_row, new_row, changes);

    IF JSON_LENGTH(changes) > 0 THEN
        INSERT INTO AuditLog (
            user_id,
			role_id,
			action,
			entity_type,
			entity_id,
			before_json,
			after_json,
			diff_json
        )
        VALUES (
            NEW.taken_by, 
			@current_role_id,
			'UPDATE',
			'Attendance',
            NEW.attendance_id,
            old_row,
		    new_row,
        	changes
        );
    END IF;
END$$
DELIMITER ;

/* INSERT TRIGGER FOR USER TABLE */

DELIMITER $$
CREATE OR REPLACE TRIGGER user_after_insert
AFTER INSERT ON `User`
FOR EACH ROW
BEGIN
    DECLARE new_row JSON;
	 DECLARE changes JSON;
	 SET new_row = JSON_OBJECT(
		 	'user_id', NEW.user_id,
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
			'user_email', NEW.user_email,
            'user_phone', NEW.user_phone,
			'user_address', NEW.user_address,
            'is_active', NEW.is_active,
			'joined_on', NEW.joined_on
        );
    INSERT INTO AuditLog (
        user_id,
		role_id,
		action,
		entity_type,
		entity_id,
        before_json,
        after_json,
		diff_json
    )
    VALUES (
         @current_user_id,
		 @current_role_id,
			'CREATE',
			'User',
            NEW.user_id,
			NULL,
		    new_row,
			changes  
    );
END$$
DELIMITER ;

/* INSERT TRIGGER FOR CALENDAR EVENT TABLE */
DELIMITER $$
CREATE OR REPLACE TRIGGER event_after_insert
AFTER INSERT ON `CalendarEvent`
FOR EACH ROW
BEGIN
    DECLARE new_row JSON;
	DECLARE changes JSON;
	 SET new_row =  JSON_OBJECT(
            'event_title', NEW.event_title,
            'event_desc', NEW.event_desc,
			'event_location', NEW.event_location,
            'event_date', NEW.event_date,
			'created_at', NEW.created_at,
			'created_by', NEW.created_by
        );
    INSERT INTO AuditLog (
        user_id,
		role_id,
		action,
		entity_type,
		entity_id,
        before_json,
        after_json,
		diff_json
    )
    VALUES (
         @current_user_id,
		 @current_role_id,
			'CREATE',
			'CalendarEvent',
            NEW.event_id,
			NULL,
		    new_row,
			changes       
    );
END$$
DELIMITER ;


/* INSERT TRIGGER FOR ANNOUNCEMENT TABLE */
DELIMITER $$
CREATE OR REPLACE TRIGGER announcement_after_insert
AFTER INSERT ON `Announcement`
FOR EACH ROW
BEGIN
    DECLARE new_row JSON;
	DECLARE changes JSON;
	 SET new_row =  JSON_OBJECT(
            'visibility_scope', NEW.visibility_scope,
            'announce_title', NEW.announce_title,
			'announce_body', NEW.announce_body,
            'announce_expiry', NEW.announce_expiry,
			'allow_opt_out', NEW.allow_opt_out,
			'announce_delivery', NEW.announce_delivery,
            'archived', NEW.archived,
			'created_at', NEW.created_at,
			'created_by', NEW.created_by
        );
    INSERT INTO AuditLog (
        user_id,
		role_id,
		action,
		entity_type,
		entity_id,
        before_json,
        after_json,
		diff_json
    )
    VALUES (
          @current_user_id,
		  @current_role_id,
			'CREATE',
			'Announcement',
            NEW.announcement_id,
			NULL,
		    new_row,
			changes 
    );
END$$
DELIMITER ;


/* INSERT TRIGGER FOR DOCUMENT TABLE */
DELIMITER $$
CREATE OR REPLACE TRIGGER document_after_insert
AFTER INSERT ON `Document`
FOR EACH ROW
BEGIN
    DECLARE new_row JSON;
	DECLARE changes JSON;
	  SET new_row =  JSON_OBJECT(
            'visibility_scope', NEW.visibility_scope,
            'doc_title', NEW.doc_title,
			'stored_url', NEW.stored_url,
            'archived', NEW.archived,
			'created_at', NEW.created_at,
			'created_by', NEW.created_by
        );
    INSERT INTO AuditLog (
        user_id,
		role_id,
		action,
		entity_type,
		entity_id,
        before_json,
        after_json,
		diff_json
    )
    VALUES (
         NEW.created_by,
		 @current_role_id,
			'CREATE',
			'Document',
            NEW.document_id,
			NULL,
		    new_row,
			changes
    );
END$$
DELIMITER ;

/* INSERT TRIGGER FOR ATTENDANCE TABLE */

DELIMITER $$
CREATE OR REPLACE TRIGGER attendance_after_insert
AFTER INSERT ON `Attendance`
FOR EACH ROW
BEGIN
    DECLARE new_row JSON;
	 DECLARE changes JSON;
	 SET new_row = JSON_OBJECT(
            'user_id', NEW.user_id,
            'event_id', NEW.event_id,
			'attend_status', NEW.attend_status,
            'check_in_time', NEW.check_in_time,
			'notes', NEW.notes
        );
    INSERT INTO AuditLog (
        user_id,
		role_id,
		action,
		entity_type,
		entity_id,
        before_json,
        after_json,
		diff_json
    )
    VALUES (
         NEW.taken_by, 
		 @current_role_id,
			'CREATE',
			'Attendance',
            NEW.attendance_id,
			NULL,
		    new_row,
			changes  
    );
END$$
DELIMITER ;

/* for auditlog testing purposes */
INSERT INTO AuditLog (
log_id,
user_id,
role_id,	
action,
entity_type,
entity_id,
before_json,			 
after_json,
diff_json,
occurred_at
)
VALUES 
(1, 1, 1,"Update","User", 1, '{"first_name":"Zendaya","last_name":"Coleman","user_email":"zend0009@email.com","user_phone":"(217) 555-0101","user_address":"101 Maple Grove Drive Springfield, IL 62704","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Zendaya","last_name":"Coleman","user_email":"daya7262@email.com","user_phone":"(217) 555-0101","user_address":"101 Maple Grove Drive Springfield, IL 62704","is_active": 1,"last_updated":"2026-02-16 10:22:00","updated_by": 1}', '{"user_email":"daya7262@email.com","last_updated":"2026-02-16 10:22:00","updated_by": 1}',"2026-02-16 10:22:00"),
(2, 11, 3,"Update","User", 11, '{"first_name":"Simone","last_name":"Biles","user_email":"sbiles12@email.com","user_phone":"(919) 555-0111","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Simone","last_name":"Biles","user_email":"simoneb1@email.com","user_phone":"(919) 555-0111","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-02-16 13:45:00","updated_by": 11}', '{"user_email":"simoneb1@email.com","last_updated":"2026-02-16 13:45:00","updated_by": 11}',"2026-02-016 13:45:00"),
(3, 20, 2,"Create","CalendarEvent", 1, NULL, '{"event_title":"First Day of Class","event_desc":"The first day of Spring Term begins","event_location":"D2L","event_date":"2026-01-20 00:00:00","created_at":"2026-02-17 10:05:00","created_by": 20}', NULL,"2026-02-017 10:05:00"),
(4, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"scj11390@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":null,"updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlett@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-17 11:33:00","updated_by": 2}', '{"user_email":"scarlett@email.com","last_updated":"2026-02-17 11:33:00","updated_by": 2}',"2026-02-017 11:33:00"),
(5, 5, 3,"Update","User", 5, '{"first_name":"Taylor","last_name":"Swift","user_email":"swift133@email.com","user_phone":"(802) 555-0105","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Taylor","last_name":"Swift","user_email":"taysw2322@email.com","user_phone":"(802) 555-0105","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-02-18 9:02:00","updated_by": 5}', '{"user_email":"taysw2322@email.com","last_updated":"2026-02-18 9:02:00","updated_by": 5}',"2026-02-018 9:02:00"),
(6, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"ari03993@email.com","user_phone":"(626) 555-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":null,"updated_by":null}', '{"first_name":"Ariana","last_name":"Grande","user_email":"arig889@email.com","user_phone":"(626) 555-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-02-18 9:07:00","updated_by": 4}', '{"user_email":"arig889@email.com","last_updated":"2026-02-18 9:07:00","updated_by": 4}',"2026-02-018 9:07:00"),
(7, 15, 3,"Update","User", 15, '{"first_name":"Zara","last_name":"Larsson","user_email":"lar23342@email.com","user_phone":"(410) 555-0115","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Zara","last_name":"Larsson","user_email":"larrsonn1@email.com","user_phone":"(410) 555-0115","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-02-18 10:44:00","updated_by": 15}', '{"user_email":"larrsonn1@email.com","last_updated":"2026-02-18 10:44:00","updated_by": 15}',"2026-02-018 10:44:00"),
(8, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"oliv2005@email.com","user_phone":"(480) 555-0110","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name": "Olivia", "last_name": "Rodrigo", "user_email": "olirodirigo@email.com", "user_phone": "(480) 555-0110", "user_address": "561 Highland Park Drive Scottsdale, AZ 85255", "is_active": 1, "last_updated": "2026-02-18 12:18:00", "updated_by": 10}', '{"user_email":"olirodirigo@email.com","last_updated":"2026-02-18 12:18:00","updated_by": 10}',"2026-02-018 12:18:00"),
(9, 20, 2,"Create","CalendarEvent", 2, NULL, '{"event_title":"Team Meeting","event_desc":"Gather to discuss future developments","event_location":"Zoom","event_date":"2026-01-23 12:30:00","created_at":"2026-02-19 14:09:00","created_by": 20}', NULL,"2026-02-019 14:09:00"),
(10, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlett@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-17 11:33:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlett@email.com","user_phone":"(608) 554-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-20 9:03:00","updated_by": 2}', '{"user_phone":"(608) 554-0122","last_updated":"2026-02-20 9:03:00","updated_by": 2}',"2026-02-020 9:03:00"),
(11, 1, 1,"Update","User", 1, '{"first_name":"Zendaya","last_name":"Coleman","user_email":"daya7262@email.com","user_phone":"(217) 555-0101","user_address":"101 Maple Grove Drive Springfield, IL 62704","is_active": 1,"last_updated":"2026-02-16 10:22:00","updated_by": 1}', '{"first_name":"Zendaya","last_name":"Coleman","user_email":"daya7262@email.com","user_phone":"(217) 555-0101","user_address":"4558 Cedar Ridge Drive Naperville, IL 60564","is_active": 1,"last_updated":"2026-02-20 10:18:00","updated_by": 1}','{"user_address":"4558 Cedar Ridge Drive Naperville, IL 60564","last_updated":"2026-02-20 10:18:00","updated_by": 1}',"2026-02-020 10:18:00"),
(12, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olirodirigo@email.com","user_phone":"(480) 555-0110","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-02-18 12:18:00","updated_by": 10}', '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olirodirigo@email.com","user_phone":"(480) 555-4432","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-02-20 11:41:00","updated_by": 10}', '{"user_phone":"(480) 555-4432","last_updated":"2026-02-20 11:41:00","updated_by": 10}',"2026-02-020 11:41:00"),
(13, 1, 1,"Update","User", 1, '{"first_name":"Zendaya","last_name":"Coleman","user_email":"daya7262@email.com","user_phone":"(217) 555-0101","user_address":"4558 Cedar Ridge Drive, Naperville, IL 60564","is_active": 1,"last_updated":"2026-02-20 10:18:00","updated_by": 1}', '{"first_name":"Zendaya","last_name":"Coleman","user_email":"zend0009@email.com","user_phone":"(217) 555-0101","user_address":"101 Maple Grove Drive Springfield, IL 62704","is_active": 1,"last_updated":"2026-02-21 9:01:00","updated_by": 1}', '{"user_email":"zend0009@email.com","user_address":"101 Maple Grove Drive Springfield, IL 62704","last_updated":"2026-02-21 9:01:00","updated_by": 1}',"2026-02-021 9:01:00"),
(14, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"ryd80189@email.com","user_phone":"(707) 555-0112","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Winona","last_name":"Ryder","user_email":"ryderw123@email.com","user_phone":"(707) 555-0112","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-02-21 9:05:00","updated_by": 12}', '{"user_email":"ryderw123@email.com","last_updated":"2026-02-21 9:05:00","updated_by": 12}',"2026-02-021 9:05:00"),
(15, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"robbie01@email.com","user_phone":"(847) 555-0109","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"","updated_by": null}', '{"first_name":"Margot","last_name":"Robbie","user_email":"marg1009@email.com","user_phone":"(847) 555-0109","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-02-21 9:09:00","updated_by": 9}', '{"user_email":"marg1009@email.com","last_updated":"2026-02-21 9:09:00","updated_by": 9}',"2026-02-021 9:09:00"),
(16, 3, 3,"Update","User", 3, '{"first_name":"Joyce","last_name":"Byers","user_email":"byers506@email.com","user_phone":"(919) 555-0103","user_address":"389 Pine Hill Lane Raleigh, NC 27607","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Joyce","last_name":"Byers","user_email":"byers506@email.com","user_phone":"(919) 555-0103","user_address":"24 Sun Crest Rd Raleigh, NC 27607","is_active": 1,"last_updated":"2026-02-21 10:44:00","updated_by": 3}', '{"user_address":"24 Sun Crest Rd Raleigh, NC 27607","last_updated":"2026-02-21 10:44:00","updated_by": 3}',"2026-02-021 10:44:00"),
(17, 5, 3,"Update","User", 5, '{"first_name":"Taylor","last_name":"Swift","user_email":"taysw2322@email.com","user_phone":"(802) 555-0105","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-02-18 9:02:00","updated_by": 5}', '{"first_name":"Taylor","last_name":"Swift","user_email":"taysw2322@email.com","user_phone":"(802) 555-1235","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-02-21 12:17:00","updated_by": 5}', '{"user_phone": "(802) 555-1235","last_updated":"2026-02-21 12:17:00","updated_by": 5}',"2026-02-021 12:17:00"),
(18, 17, 2,"Create","CalendarEvent", 3, NULL, '{"event_title":"Iftari","event_desc":"Iftari Dinner starting at 5 PM","event_location":"Mosque","event_date":"2026-03-07 18:00:00","created_at":"2026-02-22 11:33:00","created_by": 17}', NULL,"2026-02-022 11:33:00"),
(19, 5, 3,"Update","User", 5, '{"first_name":"Taylor","last_name":"Swift","user_email":"taysw2322@email.com","user_phone":"(802) 555-1235","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-02-21 12:17:00","updated_by": 5}', '{"first_name":"Taylor","last_name":"Swift","user_email":"tswifft213@email.com","user_phone":"(802) 555-1235","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-02-22 13:52:00","updated_by": 5}', '{"user_email":"tswifft213@email.com","last_updated":"2026-02-022 13:52:00:0","updated_by": 5}',"2026-02-022 13:52:00"),
(20, 11, 3,"Update","User", 11, '{"first_name":"Simone","last_name":"Biles","user_email":"simoneb1@email.com","user_phone":"(919) 555-0111","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-02-16 13:45:00","updated_by": 11}', '{"first_name":"Simone","last_name":"Biles","user_email":"simoneb1@email.com","user_phone":"(919) 557-7221","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-02-23 10:06:00","updated_by": 11}', '{"user_phone":"(919) 557-7221","last_updated":"2026-02-23 10:06:00","updated_by": 11}',"2026-02-023 10:06:00"),
(21, 6, 3,"Update","User", 6, '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"sabcarp8@email.com","user_phone":"(972) 555-0106","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"sabrina01@email.com","user_phone":"(972) 555-0106","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-02-24 9:14:00","updated_by": 6}', '{"user_email":"sabrina01@email.com","last_updated":"2026-02-24 9:14:00","updated_by": 6}',"2026-02-024 9:14:00"),
(22, 18, 1,"Create","Announcement", 1, NULL, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-02-24 11:29:00","archived": 0,"created_at":"2026-02-24 11:29:00","created_by": 18}', NULL,"2026-02-024 11:29:00"),
(23, 16, 3,"Update","User", 16, '{"first_name":"Gracie","last_name":"Abrams","user_email":"abg41207@email.com","user_phone":"(410) 555-0116","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Gracie","last_name":"Abrams","user_email":"graci43@email.com","user_phone":"(410) 555-0116","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-02-25 9:03:00","updated_by": 16}', '{"user_email":"graci43@email.com","last_updated":"2026-02-25 9:03:00","updated_by": 16}',"2026-02-025 9:03:00"),
(24, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"ryderw123@email.com","user_phone":"(707) 555-0112","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-02-21 9:05:00","updated_by": 11}', '{"first_name":"Winona","last_name":"Ryder","user_email":"ryderw123@email.com","user_phone":"(707) 555-9232","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-02-25 10:44:00","updated_by": 12}', '{"user_phone":"(707) 555-9232","last_updated":"2026-02-25 10:44:00","updated_by": 12}',"2026-02-025 10:44:00"),
(25, 13, 3,"Update","User", 13, '{"first_name":"Kylie","last_name":"Jenner","user_email":"jennnr24@email.com","user_phone":"(828) 555-0113","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Kylie","last_name":"Jenner","user_email":"kyjenn24@email.com","user_phone":"(828) 555-0113","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-02-25 12:41:00","updated_by": 13}', '{"user_email":"kyjenn24@email.com","last_updated":"2026-02-25 12:41:00","updated_by": 13}',"2026-02-025 12:41:00"),
(26, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlett@email.com","user_phone":"(608) 554-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-20 9:03:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"sjohan123@email.com","user_phone":"(608) 554-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-26 5:18:00","updated_by": 2}', '{"user_email":"sjohan123@email.com","last_updated":"2026-02-17 11:33:00","updated_by": 2}',"2026-02-026 15:18:00"),
(27, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"marg1009@email.com","user_phone":"(847) 555-0109","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-02-21 9:09:00","updated_by": 9}', '{"first_name":"Margot","last_name":"Robbie","user_email":"marg1009@email.com","user_phone":"(847) 555-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-02-27 9:02:00","updated_by": 9}', '{"user_phone":"(847) 555-1009","last_updated":"2026-02-27 9:02:00","updated_by": 9}',"2026-02-027 9:02:00"),
(28, 16, 3,"Update","User", 16, '{"first_name":"Gracie","last_name":"Abrams","user_email":"graci43@email.com","user_phone":"(410) 555-0116","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-02-25 9:03:00","updated_by": 16}', '{"first_name":"Gracie","last_name":"Abrams","user_email":"graci43@email.com","user_phone":"(410) 555-8832","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-02-27 10:11:00","updated_by": 16}', '{"user_phone":"(410) 555-8832","last_updated":"2026-02-27 10:11:00","updated_by": 16}',"2026-02-027 10:11:00"),
(29, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"sjohan123@email.com","user_phone":"(608) 554-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-26 5:18:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"sjohan123@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-27 14:33:00","updated_by": 2}', '{"user_phone":"(608) 555-0102","last_updated":"2026-02-20 9:03:00","updated_by": 2}',"2026-02-027 14:33:00"),
(30, 11, 3,"Update","User", 11, '{"first_name":"Simone","last_name":"Biles","user_email":"simoneb1@email.com","user_phone":"(919) 557-7221","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-02-23 10:06:00","updated_by": 11}', '{"first_name":"Simone","last_name":"Biles","user_email":"bsimon103@email.com","user_phone":"(919) 557-7221","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-02-28 9:05:00","updated_by": 11}', '{"user_email":"bsimon103@email.com","last_updated":"2026-02-28 9:05:00","updated_by": 11}',"2026-02-028 9:05:00"),
(31, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hai47073@email.com","user_phone":"(425) 555-0107","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"","updated_by": null}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hailee9@email.com","user_phone":"(425) 555-0107","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-02-28 9:09:00","updated_by": 7}', '{"user_email":"hailee9@email.com","last_updated":"2026-02-28 9:09:00","updated_by": 7}',"2026-02-028 9:09:00"),
(32, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"arig889@email.com","user_phone":"(626) 555-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-02-18 9:07:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"arig889@email.com","user_phone":"(626) 545-0144","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-02-28 10:22:00","updated_by": 4}', '{"user_phone":"(626) 545-0144","last_updated":"2026-02-28 10:22:00","updated_by": 4}',"2026-02-028 10:22:00"),
(33, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"marg1009@email.com","user_phone":"(847) 555-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-02-27 9:02:00","updated_by": 9}', '{"first_name":"Margot","last_name":"Robbie","user_email":"robmargo11@email.com","user_phone":"(847) 555-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-02-28 11:44:00","updated_by": 9}', '{"user_email":"robmargo11@email.com","last_updated":"2026-02-28 11:44:00","updated_by": 9}',"2026-02-028 11:44:00"),
(34, 17, 2,"Create","CalendarEvent", 4, NULL, '{"event_title":"Iftari","event_desc":"Iftari Dinner starting at 5 PM","event_location":"Mosque","event_date":"2026-03-14 18:00:00","created_at":"2026-02-28 13:18:00","created_by": 17}', NULL,"2026-02-028 13:18:00"),
(35, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"arig889@email.com","user_phone":"(626) 545-0144","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-02-28 10:22:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"arianagr@email.com","user_phone":"(626) 545-0144","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-1 9:01:00","updated_by": 4}', '{"user_email":"arianagr@email.com","last_updated":"2026-03-1 9:01:00","updated_by": 4}',"2026-03-01 9:01:00"),
(36, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"arianagr@email.com","user_phone":"(626) 545-0144","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-1 9:01:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"arianagr@email.com","user_phone":"(626) 554-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-1 10:33:00","updated_by": 4}', '{"user_phone":"(626) 554-0104","last_updated":"2026-03-1 10:33:00","updated_by": 4}',"2026-03-01 10:33:00"),
(37, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"arianagr@email.com","user_phone":"(626) 554-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-1 10:33:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"ariana093@email.com","user_phone":"(626) 554-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-1 12:47:00","updated_by": 4}', '{"user_email":"ariana093@email.com","last_updated":"2026-03-1 12:47:00","updated_by": 4}',"2026-03-01 12:47:00"),
(38, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olirodirigo@email.com","user_phone":"(480) 555-4432","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-02-20 11:41:00","updated_by": 10}', '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olivia882@email.com","user_phone":"(480) 555-4432","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-2 9:04:00","updated_by": 10}', '{"user_email":"olivia882@email.com","last_updated":"2026-03-2 9:04:00","updated_by": 10}',"2026-03-02 9:04:00"),
(39, 6, 3,"Update","User", 6, '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"sabrina01@email.com","user_phone":"(972) 555-0106","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-02-24 9:14:00","updated_by": 6}', '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"sabrina01@email.com","user_phone":"(972) 555-2296","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-2 10:18:00","updated_by": 6}', '{"user_phone":"(972) 555-2296","last_updated":"2026-03-2 10:18:00","updated_by": 6}',"2026-03-02 10:18:00"),
(40, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hailee9@email.com","user_phone":"(425) 555-0107","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-02-28 9:09:00","updated_by": 7}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hailee9@email.com","user_phone":"(425) 544-0199","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-3 11:33:00","updated_by": 7}', '{"user_phone":"(425) 544-0199","last_updated":"2026-03-3 11:33:00","updated_by": 7}',"2026-03-03 11:33:00"),
(41, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olivia882@email.com","user_phone":"(480) 555-4432","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-2 9:04:00","updated_by": 10}', '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olivia882@email.com","user_phone":"(480) 555-1555","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-4 14:09:00","updated_by": 10}', '{"user_phone":"(480) 555-1555","last_updated":"2026-03-4 14:09:00","updated_by": 10}',"2026-03-04 14:09:00"),
(42, 16, 3,"Update","User", 16, '{"first_name":"Gracie","last_name":"Abrams","user_email":"graci43@email.com","user_phone":"(410) 555-8832","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-02-27 10:11:00","updated_by": 16}', '{"first_name":"Gracie","last_name":"Abrams","user_email":"abg41207@email.com","user_phone":"(410) 555-8832","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-03-5 9:07:00","updated_by": 16}', '{"user_email":"abg41207@email.com","last_updated":"2026-03-5 9:07:00","updated_by": 16}',"2026-03-05 9:07:00"),
(43, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"robmargo11@email.com","user_phone":"(847) 555-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-02-28 11:44:00","updated_by": 9}', '{"first_name":"Margot","last_name":"Robbie","user_email":"robmargo11@email.com","user_phone":"(847) 522-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-5 10:44:00","updated_by": 9}', '{"user_phone":"(847) 522-1009","last_updated":"2026-03-5 10:44:00","updated_by": 9}',"2026-03-05 10:44:00"),
(44, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"robmargo11@email.com","user_phone":"(847) 522-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-5 10:44:00","updated_by": 9}', '{"first_name":"Margot","last_name":"Robbie","user_email":"margggo12@email.com","user_phone":"(847) 522-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-6 12:18:00","updated_by": 9}', '{"user_email":"margggo12@email.com","last_updated":"2026-03-6 12:18:00","updated_by": 9}',"2026-03-06 12:18:00"),
(45, 5, 3,"Update","User", 5, '{"first_name":"Taylor","last_name":"Swift","user_email":"tswifft213@email.com","user_phone":"(802) 555-1235","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-02-22 13:52:00","updated_by": 5}', '{"first_name":"Taylor","last_name":"Swift","user_email":"tswifft213@email.com","user_phone":"(802) 555-0105","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-03-7 9:02:00","updated_by": 5}', '{"user_phone":"(802) 555-0105","last_updated":"2026-03-7 9:02:00","updated_by": 5}',"2026-03-07 9:02:00"),
(46, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hailee9@email.com","user_phone":"(425) 544-0199","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-3 11:33:00","updated_by": 7}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"steinfi317@email.com","user_phone":"(425) 544-0199","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-7 9:06:00","updated_by": 7}', '{"user_email":"steinfi317@email.com","last_updated":"2026-03-7 9:06:00","updated_by": 7}',"2026-03-07 9:06:00"),
(47, 11, 3,"Update","User", 11, '{"first_name":"Simone","last_name":"Biles","user_email":"bsimon103@email.com","user_phone":"(919) 557-7221","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-02-28 9:05:00","updated_by": 11}', '{"first_name":"Simone","last_name":"Biles","user_email":"bsimon103@email.com","user_phone":"(919) 555-0111","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-03-7 9:11:00","updated_by": 11}', '{"user_phone":"(919) 555-0111","last_updated":"2026-03-7 9:11:00","updated_by": 11}',"2026-03-07 9:11:00"),
(48, 13, 3,"Update","User", 13, '{"first_name":"Kylie","last_name":"Jenner","user_email":"kyjenn24@email.com","user_phone":"(828) 555-0113","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-02-25 12:41:00","updated_by": 13}', '{"first_name":"Kylie","last_name":"Jenner","user_email":"kyjenn24@email.com","user_phone":"(828) 555-2009","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-03-7 10:22:00","updated_by": 13}', '{"user_phone":"(828) 555-2009","last_updated":"2026-03-7 10:22:00","updated_by": 13}',"2026-03-07 10:22:00"),
(49, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"sjohan123@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-02-27 14:33:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlettjo@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-03-7 11:41:00","updated_by": 2}', '{"user_email":"scarlettjo@email.com","last_updated":"2026-02-17 11:33:00","updated_by": 2}',"2026-03-07 11:41:00"),
(50, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"steinfi317@email.com","user_phone":"(425) 544-0199","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-7 9:06:00","updated_by": 7}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"steinfi317@email.com","user_phone":"(425) 425-3344","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-8 9:03:00","updated_by": 7}', '{"user_phone":"(425) 425-3344","last_updated":"2026-03-8 9:03:00","updated_by": 7}',"2026-03-08 9:03:00"),
(51, 16, 3,"Update","User", 16, '{"first_name":"Gracie","last_name":"Abrams","user_email":"abg41207@email.com","user_phone":"(410) 555-8832","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-03-5 9:07:00","updated_by": 16}', '{"first_name":"Gracie","last_name":"Abrams","user_email":"abg41207@email.com","user_phone":"(410) 555-0116","user_address":"32 Lakeview Crescent, Mount Pleasant, SC 29464","is_active": 1,"last_updated":"2026-03-8 9:08:00","updated_by": 16}', '{"user_phone":"(410) 555-0116","last_updated":"2026-03-8 9:08:00","updated_by": 16}',"2026-03-08 9:08:00"),
(52, 6, 3,"Update","User", 6, '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"sabrina01@email.com","user_phone":"(972) 555-2296","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-2 10:18:00","updated_by": 6}', '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"carpentr23@email.com","user_phone":"(972) 555-2296","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-8 10:44:00","updated_by": 6}', '{"user_email":"carpentr23@email.com","last_updated":"2026-03-8 10:44:00","updated_by": 6}',"2026-03-08 10:44:00"),
(53, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"steinfi317@email.com","user_phone":"(425) 425-3344","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-8 9:03:00","updated_by": 7}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hail09stein@email.com","user_phone":"(425) 425-3344","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-8 13:19:00","updated_by": 7}', '{"user_email":"hail09stein@email.com","last_updated":"2026-03-8 13:19:00","updated_by": 7}',"2026-03-08 13:19:00"),
(54, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"margggo12@email.com","user_phone":"(847) 522-1009","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-6 12:18:00","updated_by": 9}', '{"first_name":"Margot","last_name":"Robbie","user_email":"margggo12@email.com","user_phone":"(847) 555-0109","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-9 9:05:00","updated_by": 9}', '{"user_phone":"(847) 555-0109","last_updated":"2026-03-9 9:05:00","updated_by": 9}',"2026-03-09 9:05:00"),
(55, 15, 3,"Update","User", 15, '{"first_name":"Zara","last_name":"Larsson","user_email":"larrsonn1@email.com","user_phone":"(410) 555-0115","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-02-18 10:44:00","updated_by": 15}', '{"first_name":"Zara","last_name":"Larsson","user_email":"larrsonn1@email.com","user_phone":"(410) 555-6743","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-03-9 10:27:00","updated_by": 15}', '{"user_phone":"(410) 555-6743","last_updated":"2026-03-9 10:27:00","updated_by": 15}',"2026-03-09 10:27:00"),
(56, 5, 3,"Update","User", 5, '{"first_name":"Taylor","last_name":"Swift","user_email":"tswifft213@email.com","user_phone":"(802) 555-0105","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-03-7 9:02:00","updated_by": 5}', '{"first_name":"Taylor","last_name":"Swift","user_email":"swift133@email.com","user_phone":"(802) 555-0105","user_address":"76 Riverstone Court Burlington, VT 05401","is_active": 1,"last_updated":"2026-03-10 11:33:00","updated_by": 5}', '{"user_email":"swift133@email.com","last_updated":"2026-03-10 11:33:00","updated_by": 5}',"2026-03-010 11:33:00"),
(57, 18, 1,"Create","Announcement", 2, NULL, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-11 14:44:00","archived": 0,"created_at":"2026-03-11 14:44:00","created_by": 18}', NULL,"2026-03-011 14:44:00"),
(58, 19, 2,"Update","Announcement", 1, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-02-24 11:29:00","archived": 0,"created_at":"2026-02-24 11:29:00","created_by": 18}', '{"visibility_scope":"Everyone","announce_title":"Test","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-02-24 11:29:00","archived": 0,"updated_at":"2026-03-12 9:01:00","updated_by": 19}', '{"announce_title":"Test","updated_at":"2026-03-12 9:01:00","updated_by": 19}',"2026-03-012 9:01:00"),
(59, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"ryderw123@email.com","user_phone":"(707) 555-9232","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-02-25 10:44:00","updated_by": 11}', '{"first_name":"Winona","last_name":"Ryder","user_email":"11winona@email.com","user_phone":"(707) 555-9232","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-03-12 9:06:00","updated_by": 12}', '{"user_email":"11winona@email.com","last_updated":"2026-03-12 9:06:00","updated_by": 12}',"2026-03-012 9:06:00"),
(60, 19, 1,"Create","Announcement", 3, NULL, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-12 10:18:00","archived": 0,"created_at":"2026-03-12 10:18:00","created_by": 19}', NULL,"2026-03-012 10:18:00"),
(61, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"ariana093@email.com","user_phone":"(626) 554-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-1 12:47:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"ariana093@email.com","user_phone":"(626) 555-1444","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-13 12:41:00","updated_by": 4}', '{"user_phone":"(626) 555-0104","last_updated":"2026-03-13 12:41:00","updated_by": 4}',"2026-03-013 12:41:00"),
(62, 14, 3,"Update","User", 14, '{"first_name":"Lindsay","last_name":"Lohan","user_email":"linloh23@email.com","user_phone":"(609) 555-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Lindsay","last_name":"Lohan","user_email":"lohan1223@email.com","user_phone":"(609) 555-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-14 9:03:00","updated_by": 14}', '{"user_email":"lohan1223@email.com","last_updated":"2026-03-14 9:03:00","updated_by": 14}',"2026-03-014 9:03:00"),
(63, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlettjo@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-03-7 11:41:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlettjo@email.com","user_phone":"(608) 555-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-03-14 9:09:00","updated_by": 2}', '{"user_phone":"(608) 555-0122","last_updated":"2026-02-20 9:03:00","updated_by": 2}',"2026-03-014 9:09:00"),
(64, 8, 3,"Update","User", 8, '{"first_name":"Sadie","last_name":"Sink","user_email":"sink9292@email.com","user_phone":"(614) 555-0108","user_address":"918 Meadowbrook Lane Columbus, OH 43221","is_active": 1,"last_updated":null,"updated_by": null}', '{"first_name":"Sadie","last_name":"Sink","user_email":"sink9292@email.com","user_phone":"(614) 555-0108","user_address":"12 Spring Rd Columbus, OH 43221","is_active": 1,"last_updated":"2026-03-14 10:22:00","updated_by": 8}', '{"user_address":"12 Spring Rd Columbus, OH 43221","last_updated":"2026-03-14 10:22:00","updated_by": 8}',"2026-03-014 10:22:00"),
(65, 18, 2,"Update","Announcement", 1, '{"visibility_scope":"Everyone","announce_title":"Test","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-02-24 11:29:00","archived": 0,"updated_at":"2026-03-12 9:01:00","updated_by": 19}', '{"visibility_scope":"Everyone","announce_title":"Test","announce_body":"Hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-02-24 11:29:00","archived": 0,"updated_at":"2026-03-15 11:44:00","updated_at": 18}', '{"announce_body":"Hello","updated_at":"2026-03-15 11:44:00","updated_by": 18}',"2026-03-015 11:44:00"),
(66, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"11winona@email.com","user_phone":"(707) 555-9232","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-03-12 9:06:00","updated_by": 11}', '{"first_name":"Winona","last_name":"Ryder","user_email":"11winona@email.com","user_phone":"(707) 222-0920","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-03-16 9:07:00","updated_by": 12}', '{"user_phone":"(707) 222-0920","last_updated":"2026-03-16 9:07:00","updated_by": 12}',"2026-03-016 9:07:00"),
(67, 17, 2,"Create","CalendarEvent", 5, NULL, '{"event_title":"Eid","event_desc":"Program starts at 10 AM","event_location":"Mosque","event_date":"2026-03-20 10:00:00","created_at":"2026-03-16 10:33:00","created_by": 17}', NULL,"2026-03-016 10:33:00"),
(68, 8, 3,"Update","User", 8, '{"first_name":"Sadie","last_name":"Sink","user_email":"sink9292@email.com","user_phone":"(614) 555-0108","user_address":"12 Spring Rd Columbus, OH 43221","is_active": 1,"last_updated":"2026-03-14 10:22:00","updated_by": 8}', '{"first_name":"Sadie","last_name":"Sink","user_email":"sink9292@email.com","user_phone":"(614) 555-0108","user_address":"918 Meadowbrook Lane Columbus, OH 43221","is_active": 1,"last_updated":"2026-03-17 12:18:00","updated_by": 8}', '{"user_address":"918 Meadowbrook Lane Columbus, OH 43221","last_updated":"2026-03-17 12:18:00","updated_by": 8}',"2026-03-017 12:18:00"),
(69, 6, 3,"Update","User", 6, '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"carpentr23@email.com","user_phone":"(972) 555-2296","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-8 10:44:00","updated_by": 6}', '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"carpentr23@email.com","user_phone":"(972) 555-0106","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-18 9:02:00","updated_by": 6}', '{"user_phone":"(972) 555-0106","last_updated":"2026-03-18 9:02:00","updated_by": 6}',"2026-03-018 9:02:00"),
(70, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hail09stein@email.com","user_phone":"(425) 425-3344","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-8 13:19:00","updated_by": 7}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hail09stein@email.com","user_phone":"(425) 555-0107","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-18 9:08:00","updated_by": 7}', '{"user_phone":"(425) 555-0107","last_updated":"2026-03-18 9:08:00","updated_by": 7}',"2026-03-018 9:08:00"),
(71, 11, 3,"Update","User", 11, '{"first_name":"Simone","last_name":"Biles","user_email":"bsimon103@email.com","user_phone":"(919) 555-0111","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-03-7 9:11:00","updated_by": 11}', '{"first_name":"Simone","last_name":"Biles","user_email":"sbiles12@email.com","user_phone":"(919) 555-0111","user_address":"744 Brookhaven Road Chapel Hill, NC 27516","is_active": 1,"last_updated":"2026-03-19 14:22:00","updated_by": 11}', '{"user_email":"sbiles12@email.com","last_updated":"2026-03-19 14:22:00","updated_by": 11}',"2026-03-019 14:22:00"),
(72, 17, 2,"Create","CalendarEvent", 6, NULL, '{"event_title":"March Meeting","event_desc":"Program will start at 11 AM","event_location":"Mosque","event_date":"2026-03-29 10:00:00","created_at":"2026-03-20 9:05:00","created_by": 17}', NULL,"2026-03-020 9:05:00"),
(73, 15, 3,"Update","User", 15, '{"first_name":"Zara","last_name":"Larsson","user_email":"larrsonn1@email.com","user_phone":"(410) 555-6743","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-03-9 10:27:00","updated_by": 15}', '{"first_name":"Zara","last_name":"Larsson","user_email":"zara3444@email.com","user_phone":"(410) 555-6743","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-03-20 10:44:00","updated_by": 15}', '{"user_email":"zara3444@email.com","last_updated":"2026-03-20 10:44:00","updated_by": 15}',"2026-03-020 10:44:00"),
(74, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"ariana093@email.com","user_phone":"(626) 555-1444","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-13 12:41:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"ari03993@email.com","user_phone":"(626) 555-1444","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-21 9:01:00","updated_by": 4}', '{"user_email":"ari03993@email.com","last_updated":"2026-03-21 9:01:00","updated_by": 4}',"2026-03-021 9:01:00"),
(75, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"scarlettjo@email.com","user_phone":"(608) 555-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-03-14 9:09:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"johansc3n@email.com","user_phone":"(608) 555-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-03-21 9:06:00","updated_by": 2}', '{"user_email":"johansc3n@email.com","last_updated":"2026-02-17 11:33:00","updated_by": 2}',"2026-03-021 9:06:00"),
(76, 14, 3,"Update","User", 14, '{"first_name":"Lindsay","last_name":"Lohan","user_email":"lohan1223@email.com","user_phone":"(609) 555-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-14 9:03:00","updated_by": 14}', '{"first_name":"Lindsay","last_name":"Lohan","user_email":"lohan1223@email.com","user_phone":"(609) 333-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-21 10:11:00","updated_by": 14}', '{"user_phone":"(609) 333-0114","last_updated":"2026-03-21 10:11:00","updated_by": 14}',"2026-03-021 10:11:00"),
(77, 17, 2,"Create","CalendarEvent", 7, NULL, '{"event_title":"April Meeting","event_desc":"Program will start at 10 AM and continue until 2PM","event_location":"Mosque","event_date":"2026-04-11 10:00:00","created_at":"2026-03-21 11:33:00","created_by": 17}', NULL,"2026-03-021 11:33:00"),
(78, 17, 2,"Create","CalendarEvent", 8, NULL, '{"event_title":"Sunday School","event_desc":"Classes will start at 12PM","event_location":"Mosque","event_date":"2026-04-05 10:00:00","created_at":"2026-03-22 13:47:00","created_by": 17}', NULL,"2026-03-022 13:47:00"),
(79, 15, 3,"Update","User", 15, '{"first_name":"Zara","last_name":"Larsson","user_email":"zara3444@email.com","user_phone":"(410) 555-6743","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-03-20 10:44:00","updated_by": 15}', '{"first_name":"Zara","last_name":"Larsson","user_email":"zara3444@email.com","user_phone":"(410) 555-0115","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-03-23 9:03:00","updated_by": 15}', '{"user_phone":"(410) 555-0115","last_updated":"2026-03-23 9:03:00","updated_by": 15}',"2026-03-023 9:03:00"),
(80, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"olivia882@email.com","user_phone":"(480) 555-1555","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-4 14:09:00","updated_by": 10}', '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"liveeeie@email.com","user_phone":"(480) 555-1555","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-23 10:22:00","updated_by": 10}', '{"user_email":"liveeeie@email.com","last_updated":"2026-03-23 10:22:00","updated_by": 10}',"2026-03-023 10:22:00"),
(81, 14, 3,"Update","User", 14, '{"first_name":"Lindsay","last_name":"Lohan","user_email":"lohan1223@email.com","user_phone":"(609) 333-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-21 10:11:00","updated_by": 14}', '{"first_name":"Lindsay","last_name":"Lohan","user_email":"liloh009@email.com","user_phone":"(609) 333-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-24 11:41:00","updated_by": 14}', '{"user_email":"liloh009@email.com","last_updated":"2026-03-24 11:41:00","updated_by": 14}',"2026-03-024 11:41:00"),
(82, 17, 2,"Create","CalendarEvent", 9, NULL, '{"event_title":"Sunday School","event_desc":"Classes will start at 12PM","event_location":"Mosque","event_date":"2026-04-12 10:00:00","created_at":"2026-03-25 9:09:00","created_by": 17}', NULL,"2026-03-025 9:09:00"),
(83, 14, 3,"Update","User", 14, '{"first_name":"Lindsay","last_name":"Lohan","user_email":"liloh009@email.com","user_phone":"(609) 333-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-24 11:41:00","updated_by": 14}', '{"first_name":"Lindsay","last_name":"Lohan","user_email":"liloh009@email.com","user_phone":"(609) 555-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-26 10:18:00","updated_by": 14}', '{"user_phone":"(609) 555-0114","last_updated":"2026-03-26 10:18:00","updated_by": 14}',"2026-03-026 10:18:00"),
(84, 9, 3,"Update","User", 9, '{"first_name":"Margot","last_name":"Robbie","user_email":"margggo12@email.com","user_phone":"(847) 555-0109","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-9 9:05:00","updated_by": 9}', '{"first_name":"Margot","last_name":"Robbie","user_email":"robbie01@email.com","user_phone":"(847) 555-0109","user_address":"207 Lakeside Parkway Evanston, IL 60202","is_active": 1,"last_updated":"2026-03-27 12:33:00","updated_by": 9}', '{"user_email":"robbie01@email.com","last_updated":"2026-03-27 12:33:00","updated_by": 9}',"2026-03-027 12:33:00"),
(85, 7, 3,"Update","User", 7, '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hail09stein@email.com","user_phone":"(425) 555-0107","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-18 9:08:00","updated_by": 7}', '{"first_name":"Hailee","last_name":"Steinfeld","user_email":"hai47073@email.com","user_phone":"(425) 555-0107","user_address":"690 Cedar Ridge Drive Bellevue, WA 98008","is_active": 1,"last_updated":"2026-03-28 9:02:00","updated_by": 7}', '{"user_email":"hai47073@email.com","last_updated":"2026-03-28 9:02:00","updated_by": 7}',"2026-03-028 9:02:00"),
(86, 18, 2,"Delete","Announcement", 2, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-11 14:44:00","archived": 0,"created_at":"2026-03-11 14:44:00","created_by": 18}', NULL, NULL,"2026-03-028 9:07:00"),
(87, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"liveeeie@email.com","user_phone":"(480) 555-1555","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-23 10:22:00","updated_by": 10}', '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"liveeeie@email.com","user_phone":"(480) 555-0110","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-28 10:44:00","updated_by": 10}', '{"user_phone":"(480) 555-0110","last_updated":"2026-03-28 10:44:00","updated_by": 10}',"2026-03-028 10:44:00"),
(88, 6, 3,"Update","User", 6, '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"carpentr23@email.com","user_phone":"(972) 555-0106","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-18 9:02:00","updated_by": 6}', '{"first_name":"Sabrina","last_name":"Carpenter","user_email":"sabcarp8@email.com","user_phone":"(972) 555-0106","user_address":"834 Willow Bend Way Plano, TX 75024","is_active": 1,"last_updated":"2026-03-29 11:29:00","updated_by": 6}', '{"user_email":"sabcarp8@email.com","last_updated":"2026-03-29 11:29:00","updated_by": 6}',"2026-03-029 11:29:00"),
(89, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"11winona@email.com","user_phone":"(707) 222-0920","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-03-16 9:07:00","updated_by": 11}', '{"first_name":"Winona","last_name":"Ryder","user_email":"winryder@email.com","user_phone":"(707) 222-0920","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-03-30 14:09:00","updated_by": 12}', '{"user_email":"winryder@email.com","last_updated":"2026-03-30 14:09:00","updated_by": 12}',"2026-03-030 14:09:00"),
(90, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"johansc3n@email.com","user_phone":"(608) 555-0122","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-03-21 9:06:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"johansc3n@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-04-1 9:01:00","updated_by": 2}', '{"user_phone":"(608) 555-0102","last_updated":"2026-02-20 9:03:00","updated_by": 2}',"2026-04-01 9:01:00"),
(91, 4, 3,"Update","User", 4, '{"first_name":"Ariana","last_name":"Grande","user_email":"ari03993@email.com","user_phone":"(626) 555-1444","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-03-21 9:01:00","updated_by": 4}', '{"first_name":"Ariana","last_name":"Grande","user_email":"ari03993@email.com","user_phone":"(626) 555-0104","user_address":"512 Sunset Ridge Avenue Pasadena, CA 91105","is_active": 1,"last_updated":"2026-04-2 10:18:00","updated_by": 4}', '{"user_phone":"(626) 555-0104","last_updated":"2026-04-2 10:18:00","updated_by": 4}',"2026-04-02 10:18:00"),
(92, 13, 3,"Update","User", 13, '{"first_name":"Kylie","last_name":"Jenner","user_email":"kyjenn24@email.com","user_phone":"(828) 555-2009","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-03-7 10:22:00","updated_by": 13}', '{"first_name":"Kylie","last_name":"Jenner","user_email":"jennnr24@email.com","user_phone":"(828) 555-2009","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-04-3 12:41:00","updated_by": 13}', '{"user_email":"jennnr24@email.com","last_updated":"2026-04-3 12:41:00","updated_by": 13}',"2026-04-03 12:41:00"),
(93, 14, 3,"Update","User", 14, '{"first_name":"Lindsay","last_name":"Lohan","user_email":"liloh009@email.com","user_phone":"(609) 555-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-03-26 10:18:00","updated_by": 14}', '{"first_name":"Lindsay","last_name":"Lohan","user_email":"linloh23@email.com","user_phone":"(609) 555-0114","user_address":"29 Stonegate Boulevard Princeton, NJ 08540","is_active": 1,"last_updated":"2026-04-5 9:06:00","updated_by": 14}', '{"user_email":"linloh23@email.com","last_updated":"2026-04-5 9:06:00","updated_by": 14}',"2026-04-05 9:06:00"),
(94, 10, 3,"Update","User", 10, '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"liveeeie@email.com","user_phone":"(480) 555-0110","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-03-28 10:44:00","updated_by": 10}', '{"first_name":"Olivia","last_name":"Rodrigo","user_email":"oliv2005@email.com","user_phone":"(480) 555-0110","user_address":"561 Highland Park Drive Scottsdale, AZ 85255","is_active": 1,"last_updated":"2026-04-6 10:33:00","updated_by": 10}', '{"user_email":"oliv2005@email.com","last_updated":"2026-04-6 10:33:00","updated_by": 10}',"2026-04-06 10:33:00"),
(95, 15, 3,"Update","User", 15, '{"first_name":"Zara","last_name":"Larsson","user_email":"zara3444@email.com","user_phone":"(410) 555-0115","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-03-23 9:03:00","updated_by": 15}', '{"first_name":"Zara","last_name":"Larsson","user_email":"lar23342@email.com","user_phone":"(410) 555-0115","user_address":"392 Harbor Point Drive Annapolis, MD 21403","is_active": 1,"last_updated":"2026-04-8 11:44:00","updated_by": 15}', '{"user_email":"lar23342@email.com","last_updated":"2026-04-8 11:44:00","updated_by": 15}',"2026-04-08 11:44:00"),
(96, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"winryder@email.com","user_phone":"(707) 222-0920","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-03-30 14:09:00","updated_by": 11}', '{"first_name":"Winona","last_name":"Ryder","user_email":"winryder@email.com","user_phone":"(707) 555-0112","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-04-10 9:03:00","updated_by": 12}', '{"user_phone":"(707) 555-0112","last_updated":"2026-04-10 9:03:00","updated_by": 12}',"2026-04-010 9:03:00"),
(97, 2, 3,"Update","User", 2, '{"first_name":"Scarlett","last_name":"johansson","user_email":"johansc3n@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-04-1 9:01:00","updated_by": 2}', '{"first_name":"Scarlett","last_name":"johansson","user_email":"scj11390@email.com","user_phone":"(608) 555-0102","user_address":"245 Oak Valley Road Madison, WI 53711","is_active": 1,"last_updated":"2026-04-12 10:22:00","updated_by": 2}', '{"user_email":"scj11390@email.com","last_updated":"2026-04-12 10:22:00","updated_by": 2}',"2026-04-012 10:22:00"),
(98, 18, 2,"Update","Announcement", 2, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-11 14:44:00","archived": 0,"created_at":"2026-03-11 14:44:00","created_by": 18}', '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"Hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-11 14:44:00","archived": 0,"updated_at":"2026-04-14 13:19:00","updated_by": 18}', '{"announce_body":"Hello","updated_at":"2026-04-14 13:19:00","updated_at": 18}',"2026-04-014 13:19:00"),
(99, 17, 2,"Create","CalendarEvent", 10, NULL, '{"event_title":"Sunday School","event_desc":"Classes will start at 12PM","event_location":"Mosque","event_date":"2026-04-19 10:00:00","created_at":"2026-04-16 9:07:00","created_by": 17}', NULL,"2026-04-016 9:07:00"),
(100, 13, 3,"Update","User", 13, '{"first_name":"Kylie","last_name":"Jenner","user_email":"jennnr24@email.com","user_phone":"(828) 555-2009","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-04-3 12:41:00","updated_by": 13}', '{"first_name":"Kylie","last_name":"Jenner","user_email":"jennnr24@email.com","user_phone":"(828) 555-0113","user_address":"967 Autumn Crest Lane Asheville, NC 28803","is_active": 1,"last_updated":"2026-04-18 10:44:00","updated_by": 13}', '{"user_phone":"(828) 555-0113","last_updated":"2026-04-18 10:44:00","updated_by": 13}',"2026-04-018 10:44:00"),
(101, 17, 2,"Create","CalendarEvent", 11, NULL, '{"event_title":"Sunday School","event_desc":"Classes will start at 12PM","event_location":"Mosque","event_date":"2026-04-26 10:00:00","created_at":"2026-04-20 12:18:00","created_by": 17}', NULL,"2026-04-020 12:18:00"),
(102, 19, 2,"Update","Announcement", 2, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"Hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-11 14:44:00","archived": 0,"updated_at":"2026-04-14 13:19:00","updated_by": 18}', '{"visibility_scope":"Everyone","announce_title":"Test","announce_body":"Hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-03-11 14:44:00","archived": 0,"updated_at":"2026-04-22 9:05:00","updated_by": 19}', '{"announce_title":"Test","updated_at":"2026-04-22 9:05:00","updated_by": 19}',"2026-04-022 9:05:00"),
(103, 12, 3,"Update","User", 12, '{"first_name":"Winona","last_name":"Ryder","user_email":"winryder@email.com","user_phone":"(707) 555-0112","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-04-10 9:03:00","updated_by": 11}', '{"first_name":"Winona","last_name":"Ryder","user_email":"ryd80189@email.com","user_phone":"(707) 555-0112","user_address":"1205 Redwood Springs Circle Santa Rosa, CA 95404","is_active": 1,"last_updated":"2026-04-23 10:11:00","updated_by": 12}', '{"user_email":"ryd80189@email.com","last_updated":"2026-04-23 10:11:00","updated_by": 12}',"2026-04-023 10:11:00"),
(104, 17, 2,"Delete","Announcement", 1, '{"visibility_scope":"Everyone","announce_title":"Testing","announce_body":"hello","announce_expiry":"2026-04-29 01:19:00","allow_opt_out": 0,"announce_delivery":"2026-02-24 11:29:00","archived": 0,"created_at":"2026-02-24 11:29:00","created_by": 18}', NULL, NULL,"2026-04-024 9:02:00");



/* auto cleanup event */
SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS delete_audit_logs
ON SCHEDULE EVERY 1 DAY
DO
  DELETE FROM AuditLog
  WHERE occurred_at < NOW() - INTERVAL 90 DAY;

ALTER TABLE CalendarEvent
ADD COLUMN is_recurring TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN recurrence_type ENUM('none','daily','weekly','monthly','yearly') NOT NULL DEFAULT 'none',
ADD COLUMN recurrence_interval INT NOT NULL DEFAULT 1,
ADD COLUMN recurrence_count INT NULL,
ADD COLUMN recurrence_end_date DATETIME NULL,
ADD COLUMN recurrence_days_of_week VARCHAR(20) NULL,
ADD COLUMN series_id INT NULL,
ADD COLUMN is_cancelled TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE CalendarEvent_Exception (
    exception_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    occurrence_date DATETIME NOT NULL,
    action_type ENUM('cancelled','edited') NOT NULL,
    new_event_date DATETIME NULL,
    override_title VARCHAR(255) NULL,
    override_desc TEXT NULL,
    override_location VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by INT NULL,
    FOREIGN KEY (event_id) REFERENCES CalendarEvent(event_id)
);


/* to access the database */
FLUSH PRIVILEGES;

CREATE USER IF NOT EXISTS 'mgs_user'@'localhost'
IDENTIFIED BY 'pa55word';

GRANT SELECT, INSERT, UPDATE, DELETE
ON *
TO 'mgs_user'@'localhost';
