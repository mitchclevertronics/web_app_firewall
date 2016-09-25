#Web App Firewall
##Introduction
WAFs goal is protect sites against hackers and virus attacks. 
Web App Firewall its PHP application that implement principle of reverse-proxy , control of types variables accepted by server , and comfortable management interface.<br>
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map0.jpg?raw=true)<br>
W.A.F. supported to work under LAMP servers with .htaccess files support.
Security protection based on white-list strategy: after starting "Learn" mode program collect map of requests, and user have to approve requests. After starting "Guard" mode - program accept only known requests.

Program using white-list strategy, it is more absolute protection, but its requires a lot of work on configuration.
In the program using Intellectual grafical UI	, its give an opportunity regularize most chaotic structure.

##Dependense
Require Linux server, Apache , PHP installed with MySQL and CURL support 
##How its working?
Web App Firewall organize reverse-proxy by injection to .htaccess file, and writing Rewrite Rules with security key 1.

Script get redirected request and parse path and parameters sent from user. Detect created rules for specified situation and block or accept request via prepared politics.

If request approved, WAF script sending request  back to server via CURL with added security key 2 (.htaccess rule miss request if detect key2).
If request blocked, WAF save logs and show 404 page.
<img src="https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/reverse_proxy.png?raw=true">
##Getting Started	
###Installation
First - upload software to web-server, for example "web_app_firewall" folder.

Open in browser http://yousite /web_app_firewall and install W.A.F.

Installation is very simple:

<b>DB Host</b>     - MySQL host or ip<br>
<b>DB User</b>     - MySQL Username<br>
<b>DB Password</b> - MySQL Password<br>
<b>DB Name</b>     - MySQL database name<br><br>
<b>Create New Db</b> - set command to create DB Name, dont need for existed database.<br>
<b>Save Old Data</b>  - New Installation : refresh data in existed database.<br>
<b>Save Old Data</b>  - Keep old data : - dont refresh data of existed database, just connect program to DB.<br>
<b>First User - Emal</b> - administration email and username<br>
<b> Password </b>- administration password<br>
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/inst1.jpg?raw=true)

If all done you see message:
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/inst2.jpg?raw=true)

Installation impossible again from now, if you need run installation wizard again - remove file <b>config.inc.php</b> , in folder inc.

###HTACCESS Injection
Now need make process of htaccess injection, click in menu on HTACCESS tab.
In top window you see code prepared for injection. In bottom - code of main .htaccess on your web resource. Need copy code from top window to bottom and Save.  If you do new injection on htaccess that contains already WAF injection code - need change it by new code.
Now traffic of you site going via reverse-proxy of the program.<br>
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/htaccess1.jpg?raw=true)
###Config Settings
<b>WAF Status Learn</b> - program building map of site only.<br>
<b>WAF Status Guard</b> - program filter only approved requests with approved variables.<br>
<b>Security Key</b> and <b>Security Key2</b> - using for orginise htaccess hook with redirect trafic to WAF and trafic from WAF to site.<br>
If generated new keys need immidiatly inject them to htaccess<br>
<b>404 Page URL</b> - address that showed for potencial attacker then request stoped from security reason.<br>
<b>Brute Force Frequency</b> - seconds between requests from same IP to segment guarded by BF option.<br>
<b>Brute Force Attempts</b> - how many times will be detected BruteForce before IP will be blacklisted.<br>

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
For fast open Segment Form for selected segments - use double click on map screen or click on icon <img src="https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/edit.png?raw=true" width="40"> in right side.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map3.jpg?raw=true)

If you want using exactly path leave Original Path (in example static php files not changing  so I can set Original Path).

If you know that possible make automatic-filter on type - switch to AutoType.

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map4.jpg?raw=true)

You have to set static part of word before or after you automatic type. Maximum size of value, and in Contains you set that can be approved in your filter, letters, numbers and Special chars - anything that you need additional for approve segment.
Set Approved - on - its make filter active in Guard Mode, BF - if you want set BruteForce detection in some script.
####Variables Form
For open Segment Variables list - double click on segment: 

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map5.jpg?raw=true)

Same cursor tools interface for select variables. We have select size, and Contains before approve.
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map6.jpg?raw=true)

<b>Make Global</b>  - special checkbox, its make variable shared for all segments. After saving global variables moving to <b>Global Variables</b> Menu (Click on Icon  <img src="https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/vars.png?raw=true" width="40"> ) 

Bottom of Access Map page you have form for sorting segments:

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map88.jpg?raw=true)

###Bad Requests
Logs of stopped attacks,  the logs recording only in <b>Guard Mode</b>. Its helpfully for learn structure and attack situations.

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map9.jpg?raw=true)
