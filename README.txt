This is place holder for README file REVISED
Sprint 4 
Access the webpage at https://capstone.ongkg.com/capstone/
Login with username ong92990@email.com and password ong92990@email.com
Features Implemented
Allows users to create, manage, and track events, including advanced support for recurring events, cancellations, and rescheduling individual occurrences.

Bugs
1. File Path Issues (Folder Structure)

When moving calendar files into a separate /calendar/ folder, some files initially failed to load due to incorrect relative paths.

Example issue:

require_once './include/db_connect.php';

Fix:

require_once '../include/db_connect.php'; 

2. Case Sensitivity on Linux Server

The application behaved differently when deployed to the Linux server compared to local development.

Example issue:

FROM calendarevent  --  not found
FROM CalendarEvent  --  correct

Cause:

Linux file system and MySQL table names are case-sensitive
Local environments (e.g., Windows) are often case-insensitive

Impact:

Caused "Table not found" errors in production

Fix:

Standardized all table references to match exact database casing (CalendarEvent, CalendarEvent_Exception)
