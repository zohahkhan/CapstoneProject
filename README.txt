Sprint #6:
Project Description: This project is a reporting system for the Al-Nur Mosque. It works by combining the current files/reports that are scattered into one website to create an easier flow for all members. This week, the attendance and form report for all users to complete was implemented. 

SetUp Instructions: To run these branches, start by connecting to XAMPP. You can open the specific branch in GitHub Desktop by choosing the 'Open with GitHub Desktop' option when you click on the green 'code' button. Then fetch the latest code, and open it in File Explorer. Copy the selected file into your htdocs folder so it can be opened via XAMPP. Ensure the database is connected, if not that can be configured at: http://localhost/phpmyadmin/. Then you can start on the login page at http://localhost/CapstoneProject/loginpages/login.php and test the user story. 

Each branch correlates with the following user stories:

Branch Name #1: feature/style
User Story: As an Member, I want to see a preview of each item/box on each web page before clicking on it so I know what information to expect.
Usage Instructions: Log in to the website and navigate to any users page, admin, member, department head, or president. Each page should have filled in boxes showing previews of that specific items or a text explanation so the user knows what to expect. 
Acceptance Criteria:
▪ A preview is displayed within each box to fill up the white space
▪ Previews should be organized and easy to read
▪ Initial text should be visible without having to click or navigate to a different screen
Test Credentials:
Username: kha27882@email.com
  Password: kha27882
  or any username/password within the database

Branch Name #2: consolidate-documents 
User Story: As a Admin, I want to debug the code so I have a fully functioning website.
Usage Instructions: Log in to Admin page to see updated logs for activity tracking.
Acceptance Criteria:
▪ Clean up the GitHub repository, delete old branches, consolidate code into develop, ensure develop branch runs smoothly. 
▪ Update ERD to match the tables in the database script. 
▪ Update User Stories document to match the current user stories.
▪ Load all code and webpages with no errors, add error handling 
Test Credentials:
  Username: kat44977@email.com
  Password: kat44977
  or any Admin username/password within the database

Branch Name #3: feature/announcements
User Story: As a President and Department Head, I want to create reminders (title, body, expiry) so that members receive timely updates.
Usage Instructions: Navigate to the President or Department Head homepage. An Announcements box displays the current count of active announcements and a preview of upcoming ones. Click "Create Announcement" to open the creation form. Fill in the title, body, and an expiry date and time, then click Publish. You will be redirected to the Manage Announcements page with a success message. From the Manage Announcements page, active announcements can be edited, closed, or deleted. Expired announcements are listed separately and can be reopened with a new expiry date or deleted. Members can view all active announcements from their homepage via the Announcements box.
Acceptance Criteria:
▪ Required fields: title (≤50 chars), body (≤2,000 chars), expiry (date/time)
▪ On publish, the announcement appears in the member dashboard
▪ Expired announcements auto-hide; status shows Active/Expired
▪ President and Department Head can manage, edit, close, reopen, and delete 
Test Credentials:
  Username: gil42134@email.com
  Password: gil42134
  or any Admin username/password within the database

Branch Name #4: 
User Story: 
Usage Instructions: 
Acceptance Criteria:
Test Credentials:
