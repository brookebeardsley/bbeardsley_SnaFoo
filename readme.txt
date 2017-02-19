Hi There!

This sample is built off of Code Igniter 3.1.3 and does contain some Angular.js

/* ************************************ */
/* A brief overview of the directories: */
/* ************************************ */

    /application            : This is where I put my active code.  Subdirectories with my code include:
        /config             : I only updated the following files - autoload.php, config.php, database.php, routes.php
        /controllers
        /helpers
        /models
        /views
            /common         : contains the temnplating information
            /snack          : contains templates relating to the "Snack" controller.
      
    /assets                 : generally external files that have been provided.
        /assets/styles/app.css      - My tweaks in addition to the provides modern.css file provided
    /database_information   : I've placed a blank backup the MySql database here for you to install to a location of your choice
    /system                 : Base libraries from Code Igniter.  I have not modified any of these.

/* ************************************ */
/* Installation                         */
/* ************************************ */
- Copy in the files from github (https://github.com/brookebeardsley/bbeardsley_SnaFoo.git) (please let me know when you want me to take this one down)
- Update the .htaccess file (if needed)
- Install the database (see below)
- Login!

- public site is posted at http://bbeardsley-snafoo.000webhostapp.com/
  (Note: this is a free hosting provider... and so they seem to be a bit slow)

/* ************************************ */
/* Required database setup              */
/* ************************************ */

To install, the following are required:
-  The database `nerdery_sample_beardsley` (see the associated .sql file in /database_information)
-  A MySQL user to access the database:
      user:     `nerdery_user`
      password: `HappinessIsAFullStomach`
      permissions: select, insert, update, delete on `nerdery_sample_beardsley`
   These credentials are also stored in /application/config/database.php

/* ************************************ */
/* Notes / Assumptions / Suggestions:   */
/* ************************************ */

-  For user tracking / voting, I am assuming that Nerdery Employees are honorable and will not try to actively game the system (logging on with a different browser, clear their cookies, different device, or using a fellow employee's device).  For additional security, I recommend some form of user identification and authentication (OAUTH?)
-  If the current "authentication" practice is kept, it would be nice to request Names from users if their current name is "New User"
-  AngularJS was only used on the "voting" page.  Voting transactions on this page are AJAX'd.
-  The database isn't encrypted.. again, because we're assuming that those who would access the database are fundamentally honest.  If not, then we can encrypt the database too.
-  Links to the "Suggestions" page will change after the user has made a suggestion.
-  I assume that a "month" starts on the first day of the Gregorian calendar month.
-  Sections with no data are hidden (Electable Snacks and Potential Snacks)
-  Shopping list will not return snacks with 0 votes.  (I don't know if this was intended.. but if a user suggests a snack, then they should also be required to automatically spend a vote on it.)
-  Also recommend that "suggestions" be not allowed if the user does not have any remaining votes.
-  I recommend that the "Snack Location" field be widened as to accomodate urls.  (Then we can automate their ordering online, as well as use iframes to allow the user to over over an item and view its details / page.)
-  It would be nice for the web application to have access to some sort of snack inventory .. why buy "Twinkies" if there are already 100 boxes on hand?  Would also make it possible for the shopping list to provide a suggested quantity based on prior use and votes.  (Electable items with higher votes would have a higher requested quantity than those with fewer votes)
-  Recommend that the web application not proviude a "maintenance" message / lock out the user if the webservice is down.  Since all data is capable of being held in the webapp, let the user(s) keep using it, then run a sync to the webservice at a later time (possibly as a cronjob).  Similar note: I don't think it is truly necessary to call the webservice _every_ time the voting / shopping list pages are opened.. I suspect that running it only a few times a day would be plenty (also as a cronjob).
-  To expand for multiple offices, we would need to:
   - review the specifications (would an "Always" snack for Chicago also be an "Always" snack for Bloomington?)
   - create an "offices" table
   - enhance the users table: add an "office" field
   - break apart the "snacks" table into "snacks" and "snacks_offices", and then move some fields over (namely the "snack_purchasing_status_id" field)
   - Update the model to be "office" sensitive.
