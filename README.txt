Sprint #3: 
Project Description: This project is a reporting system for the Al-Nur Mosque. It works by combining the current files/reports that are scattered into one website to create an easier flow for all members. This week, the attendance and form report for all users to complete was implemented. 

SetUp Instructions: To run these branches, start by connecting to XAMPP. You can open the specific branch in GitHub Desktop by choosing the 'Open with GitHub Desktop' option when you click on the green 'code' button. Then fetch the latest code, and open it in File Explorer. Copy the selected file into your htdocs folder so it can be opened via XAMPP. Ensure the database is connected, if not that can be configured at: http://localhost/phpmyadmin/. Then you can start on the login page at http://localhost/CapstoneProject/loginpages/login.php and test the user story. 

Each branch correlates with the following user stories: 

Branch Name #1: feature/reportResponses
User Story: As a Department Head, I want to generate a monthly summary (counts by category, charts) from submitted reports. 
Usage Instructions:  Navigate to the department head page, and in the 'monthly report responses' box, there should be a smaller box within that shows the total number of forms completed so far. Underneath that, there should be a 'view summary' hyperlink. Since the box is small and limits what can be shown, I decided to create a new page to display the summary information. This screen should show the user pie charts of the members reponses. After the department head goes through the charts, there is a 'back to homepage' button that returns the user to their homepage.
Acceptance Criteria:
  ▪ Filters: month, question. 
  ▪ Outputs: on-screen chart.
  ▪ Query returns ≤ 5 sec for last 12 months. 
  ▪ Data source defined: internal forms.
Test Credentials:
  Username: kha27882@email.com
  Password: kha27882
  or any username/password that has department head access

Branch Name #2:
User Story: 
Usage Instructions: 
Acceptance Criteria:
Test Credentials: 

Branch Name #3:
User Story: 
Usage Instructions: 
Acceptance Criteria:
Test Credentials: 

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
