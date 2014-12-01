#Canary NDTMS Readme

---

Canary is a case management and reporting system for organisations that provide structured drug and alcohol support for adults. It also has the facility to export to the National Drug Treamtent Monitoring Service (NDTMS)

Setup
---
To setup Canary NDTMS, you first need to edit your configuration files. We are using the codeignter environment folders for specific configurations, such as database configuration. More information on this is found here https://ellislab.com/codeigniter/user-guide/libraries/config.html#environments
  
The files that you will need to edit for your environment are as follows;

- `config.php`
	- base_url
	- site_name
	- site_email
	- service_address
	- salt
	- password_expiry
	- cron_key
- `database.php`
	- hostname
	- username
	- password
	- database

All other settings in these files should be OK for your setup, but feel free to change them if you know what you are doing.
  
Once you have these files setup, you will need to create your database (defined in the database.php config file), and import the install.sql file (found under the SQL folder).  
After importing the install.sql file, you can then run the deploy script to setup and additional changes and the inital test administrator, and test user. Details of these users can be found below.
  
Now that you have done the above setup, you should be presented with a login screen when you visit the site.

The username for the example account is as follows;

- `admin@example.com`

Default password is `Passw0rd`

License
---
GNU AFFERO GENERAL PUBLIC LICENSE - Version 3  
See the LICENSE file


Copyright
---
Copyright (c) 2014 Cloud Data Service Ltd. All rights reserved. See license file for more information
