Sprint #2: 
The new feature branches that were created were profile, edit-login, and new-user. To run these branches, you must connect to XAMPP, and configure the file in htdocs and ensure the database connection.
Each branch correlates with the following user stories: 


Profile branch:
User Story: As a Member, I want to view and update my personal profile so that my contact information and preferences are correct.
Usage Instructions: The new files created were updateProfile and updateProfileForm. You should be able to log in with any credentials, and at the bottom of your homepage, there should be an 'update profile' hyperlink. That should take you to the screen where you can edit information. Click the 'update profile' button and then it should take you to a screen where it tells you if it was successful or not. If it was not successful, you will need to click the back arrow and make the necessary changes to your profile information. If it was successful, after two seconds, it should take you back to your homepage. 
 Acceptance Criteria:
-  Required fields: first name, last name, email, phone number, street address.
-  Users can only view and update their own profile.
-  Profile must show “last updated” timestamp and logs user ID for changes.
-  If the update is successful, the system must display the changes within 2 seconds of clicking the button to confirm changes (page auto refreshes)
-  If the update is unsuccessful, such as if the session times out prior to confirming changes or invalid field input, the system must display an error message that states what went wrong. 
Test Credentials: Username: kha27882@email.com Password: kha27882 or any username/password already the database

User-roles branch:
User Story: As an Admin, I want to assign user roles and grant permissions so that access is properly controlled.
Usage Instructions: The new files created were manage_roles.php and assign_role_process.php, along with database schema updates (Role, UserRole, Permission, RolePermission, and RoleChangeLog tables). Log in with admin credentials, and on the homepage, you should see a '⚙ Manage User Roles & Permissions' hyperlink. Click it to access the role management interface where you can view all active users and their current roles. Select a role from the dropdown menu next to any user and click 'Assign Role' to update their role. The system will display a success message and log the change in the RoleChangeLog table with timestamp, admin ID, and before/after role status. The page also displays the permissions associated with each role (Create, Read, Update, Delete) in the Role Permissions table.
Acceptance Criteria:
- Assign user roles (President/Dept Head/Member/Admin) and grant permissions to these roles (Create/Read/Update/Delete).
- Role changes will log the before and after status of the user, user ID, admin ID, and timestamp.
- Updates to roles/permissions take effect immediately.
Test Credentials: Username: carie_b0009@email.com Password: password (or any admin account - all test users default to Admin role)
