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
