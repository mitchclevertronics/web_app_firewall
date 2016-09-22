#Web App Firewall
##Introduction
Goal of the software is protect sites against hackers and virus attacks. 
Web App Firewall its PHP application that implement principle of reverse-proxy and control of types variables accepted by web-site from one side, and comfortable interface from part of management. W.A.F. supported to work under LAMP servers with .htaccess files support.
Security protection based on white-list strategy: after starting "Learn Mode" program collect map of requests, and user have to configure map of requests by setting variable type and size. After starting "Guard Mode" - program accept only known requests via formula of var-type or exactly value.
##Pre-requisites
Program using white-list strategy, it is more absolute protection, but its requires a lot of work on configuration.
In the program using a new approach for UI	, and give an opportunity regularize most chaotic structure. And as a result of a security setting on your site.  
##Getting Started	
First - upload software to web-server, for example "web_app_firewall" folder.

Open in browser http://yousite /web_app_firewall and install W.A.F.

Installation is very simple:

DB Host - MySQL host or ip

DB User - MySQL Username

DB Password - MySQL Password

DB Name - MySQL database name


Create New Db - set command to create DB Name, dont need for existed database.

Save Old Data  - New Installation : refresh data in existed database.

Save Old Data  - Keep old data : - dont refresh data of existed database, just connect program to DB.

First User - Emal: administration email and username

Password - administration password
