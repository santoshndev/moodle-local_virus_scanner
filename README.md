# Virus Scanner #

This plugin can be used to scan the uploaded assignment files.

# #Features

  #### #1. Scan uploaded assignment files daily
  - This plugin will scan the uploaded assignment submission files daily.
  #### #2. Send virus scan report mail
  - This plugin will send the mail to the specified mail id only if the virus is detected.
  #### #3. Help to reduce cpu utilization by scheduling the task at specified time
  - This plugin will create a scheduled task to be run at specified time when server is idle.
  

# #Original Author

Author: Santosh Nagargoje 

Web profile: https://santoshnagargoje.in/

# #Installation:

You can download the plugin from moodle plugin directory
- install clamscan and update virus database on your web server (it's must otherwise plugin will not work)

###### Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  
 
1. Login as an administrator and put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the
    administrator.
2. Copy the extracted folder to the '/local/' folder.
3. Check the scheduled task created in Site Administration -> Server -> Scheduled Task
