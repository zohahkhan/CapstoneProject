Sprint #5:
Project Description: This project is a reporting system for the Al-Nur Mosque. It works by combining the current files/reports that are scattered into one website to create an easier flow for all members. This week, the attendance and form report for all users to complete was implemented. 

SetUp Instructions: To run these branches, start by connecting to XAMPP. You can open the specific branch in GitHub Desktop by choosing the 'Open with GitHub Desktop' option when you click on the green 'code' button. Then fetch the latest code, and open it in File Explorer. Copy the selected file into your htdocs folder so it can be opened via XAMPP. Ensure the database is connected, if not that can be configured at: http://localhost/phpmyadmin/. Then you can start on the login page at http://localhost/CapstoneProject/loginpages/login.php and test the user story. 

Each branch correlates with the following user stories:

Branch Name #1: 
User Story: 
Usage Instructions:  
Acceptance Criteria:
Test Credentials:


Branch Name #2: feature/reviewSuggestions
User Story: As a Department Head & President, I want to be able to review suggestions or feedback given.
Usage Instructions:  Navigate to the Department Head or President homepage. A suggestion preview card will display the total number of pending and reviewed/resolved suggestions. Click "Review Suggestions" to open the full review page. On the review page, all submitted member feedback is displayed in an organized table showing the submitter's name, role, suggestion text, any attachments, date submitted, and current status. Department Heads and Presidents can mark suggestions as resolved or reopen them. The President can additionally approve suggestions submitted by Department Heads. All status changes display who resolved or approved the suggestion by name and role. Suggestions can also be selected individually or all at once and deleted.
Acceptance Criteria:
- Ability to review all submitted feedback
- Mark suggestions as resolved
- Have all suggestions displayed in an organized format
- President can approve suggestions Department Head submits
Test Credentials:
Username: gil42134@email.com
Password: gil42134
or any username/password within the database


Branch Name #3: feature/audit-log
User Story: As an Admin, I want to view system logs so that I can monitor webapp activities and for troubleshooting issues.
Usage Instructions:  
     Starting on the Admin dashboard (index.php), click the view logs link that launches viewLog.php page with all the logs displayed in a table. The logs (in viewLog.php) can be filtered by the user ID of who made the changes, the role they were signed into at the time of making the changes, the action taken (create, read, update, delete), and the date of when the changed were made. Each log contains more information by clicking the corresponding view more button that launches auditLogInfo.php page. Additionally, in viewLog.php, the Admin user can chose to export all the logs into a CSV file. I also made an exportAuditLogs.php file that should automatically export the AuditLog table to exports folder in my htdocs every 89 days (1 day before purged via database script).    
Acceptance Criteria:
  ▪ Log entries must include timestamp, user ID, action, status. 
  ▪ Filters: date, user ID, action, role. 
  ▪ System logs are auto purged after 90 days to save on storage; individual  logs cannot be manually deleted. 
  ▪ Use a Node.js script that is scheduled to query logs, generate CSV/PDF,  and export to a designated folder. 
Test Credentials: 
  Username: kat44977@email.com
  Password: kat44977
  or any user with Admin role permissions within the database


Branch Name #4:
User Story: 
Usage Instructions:  
Acceptance Criteria:
Test Credentials: 


Workflow Info: The main branch houses all our documents about preparation and planning from the first semester for easy reference. The develop branch is the most up-to-date version of our code; it is the final branch that all code branches merge into. We use the feature/branch-name naming strategy for our pull requests and branch naming for a uniform naming format. While all members are encouraged to review all pull requests, we have assigned each member the specific responsibility to review another member's pull requests: 
Zoha will review JJ's PR
JJ will review Shannon's PR
Shanon will review Kah's PR 
Kah will review Zoha's PR
