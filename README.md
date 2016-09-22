#Web App Firewall
##Introduction
Goal of the software is protect sites against hackers and virus attacks. 
Web App Firewall its PHP application that implement principle of reverse-proxy and control of types variables accepted by web-site from one side, and comfortable interface from part of management. W.A.F. supported to work under LAMP servers with .htaccess files support.
Security protection based on white-list strategy: after starting "Learn Mode" program collect map of requests, and user have to configure map of requests by setting variable type and size. After starting "Guard Mode" - program accept only known requests via formula of var-type or exactly value.
##Pre-requisites
Program using white-list strategy, it is more absolute protection, but its requires a lot of work on configuration.
In the program using a new approach for UI	, and give an opportunity regularize most chaotic structure. And as a result of a security setting on your site.  
##Getting Started	
###Installation
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
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/inst1.jpg?raw=true)

If all done you see message:
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/inst2.jpg?raw=true)

Installation impossible again from now, if you need run installation wizard again - remove file config.inc.php , in folder inc.

###HTACCESS Injection
Now need make process of htaccess injection, click in menu on HTACCESS tab.
In top window you see code prepared for injection. In bottom - code of main .htaccess on your web resource. Need copy code from top window to bottom and Save.  If you do new injection on htaccess that contains already WAF injection code - need change it by new code.
Now traffic of you site going via reverse-proxy of the program.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/htaccess.jpg?raw=true)
###Config Settings
WAF Status Learn: program building map of site only.
WAF Status Guard: program filter only approved requests with approved variables.
Security Key and Security Key2: Using for orginise htaccess hook with redirect trafic to WAF and trafic from WAF to site.
If generated new keys need immidiatly inject them to htaccess
404 Page URL: address that showed for potencial attacker then request stoped from security reason.
Brute Force Frequency: - seconds between requests from same IP to segment guarded by BF option.
Brute Force Attempts: - how many times will be detected BruteForce before IP will be blacklisted.

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/settings1.jpg?raw=true)

Set W.A.F Status Learn on, and Guard off.
Now program start collect request-map from every request to site, leave it for one week for view more complete structure of site.

##Support
###Access Map
In Access Map you can see recorded structure of site. In {#} - number of variables connected to segment. Tag BF in {} - used protection againts brute force.
If segment red - its new not approved segment, if green - approved.
Point cursor on segment - you can see list of variables connected to segment.

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map1.jpg?raw=true)
####How select segments for edition - Cursor tools menu:
In right side of screen you can see Map menu, its changing cursor function:

<img src=https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/pencil.png width=40> - Pencil make your cursor selectable, so you can choice segments by pointing cursor.
Selected segments change color to lime.

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map2.jpg?raw=true)

<img src=https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/eraser.png width=40> - Eraser - give opportunity to disable selection.

<img src=https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/edit.png width=40> - Then you finish to select segments  -  click Edit Form icon or double click on screen for open Segment Form for selected elements.

Another opportunity to change Cursor tools - make right click on the map screen, its rotate the tool.

Additional in menu:
<img src=https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/vars.png width=40> - Opening Global Vars list 
<img src=https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/roger.png width=40> - Remove map.

You can change position of segments by drag n drop and program record the position.

####Segments Form
For fast open Segment Form for selected segments - use double click on map screen or click on icon ![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/edit.png?raw=true) in right side.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map3.jpg?raw=true)
If you want using exactly path leave Original Path .(in example static php files not changing  so I can set Original Path)
If you know that possible make automatic-filter on type - switch to AutoType.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map4.jpg?raw=true)
You have to set static part of word before or after you automatic type. Maximum size of value, and in Contains you set that can be approved in your filter, letters, numbers and Special chars - anything that you need additional for approve segment.
Set Approved - on - its make filter active in Guard Mode, BF - if you want set BruteForce detection in some script.
####Variables Form
For open Segment Variables list - double click on segment: 
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map5.jpg?raw=true)
Same cursor tools interface for select variables. We have select size, and Contains before approve.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map6.jpg?raw=true)
Make Global  - special checkbox, its make variable shared for all segments. After saving global variables moving to Global Variables Menu (Click on Icon  ![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/vars.png?raw=true) ) 
Bottom of Access Map page you have form for sorting segments 
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map8.jpg?raw=true)
###Bad Requests
Bad Requests: here possible read logs of stopped attacks,  the logs recording only in Guard Mode. Its helpfully for learn structure and attack situations.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map9.jpg?raw=true)
