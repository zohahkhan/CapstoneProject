Sprint #1: 
The new feature branches that were created were homepages, loginpages, password-recovery, and session-management-system. Connect to XAMPP, and configure the file in htdocs. A user should be able to login securely, logout securely, view different pages, and reset their password. Each branch correlates with the following user stories: 


Login-pages branch: 
As a User (President/Head/Member/Admin), I want my login experience to be secure and authenticated so that my account role is only available to authorized personnel. - High Priority, Shannon and Kah
o   Acceptance Criteria:
- Login verification that matches the unique email to stored password.
-  Error handling that lets the user know if their login attempt was successful or not, but not specifically whether the user/password was correct/incorrect (to deter hacking).
- If successful, the roles are automatically routed from the server-side based on credentials entered in the login page.
-  If unsuccessful, users are temporarily locked out after 5 failed attempts and must wait 2 hours before trying again.

Password-Recovery branch: 
As a User (President/Head/Member/Admin), I might forget or lose my password and want to be able to reset my password just in case, so that I can safely recover access to my account. - High Priority, JJ
o   Acceptance Criteria:
-A tokenized link is sent to the user’s email, prompting the user to enter and confirm their new password.
-  User must enter the token in a box after opening the link in email to verify they match, expires after 3 minutes.
-  Strong passwords use regular expressions (regex) to set security criteria (i.e., length min 8 characters, min of 2 numbers, min of 1 special character symbol, must start with a capital letter).
-  Passwords are hashed when stored in the database.

Session Management System Branch: 
As a User (President/Head/Member/Admin), I want to terminate my account’s session by logging out so that I can protect my information from the outside, especially if I use an untrusted device. - High Priority, Shannon
o   Acceptance Criteria:
- Display message that lets the user know the session has successfully ended.
- Redirects to main menu page with the option to log in again.

Homepages: 
As a User (President/Head/Member/Admin), after I log in, I want to see a personalized dashboard so that I can quickly navigate relevant functions for my role and to know at first glance what the platform offers. - High Priority, Zoha
o   Acceptance Criteria:
-  The user is welcomed with their name and role in the top banner.
-  Organized list of links to easily access what the user wants to do.
