#Web App Firewall
##Introduction
WAFs goal is protect sites against hackers and virus attacks. 
Web App Firewall its PHP application that implement principle of reverse-proxy , control of types variables accepted by server , and comfortable management interface.<br>
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map0.jpg?raw=true)<br>
W.A.F. supported to work under LAMP servers with .htaccess files support.
Security protection based on white-list strategy: after starting "Learn" mode program collect map of requests, and user have to approve requests. After starting "Guard" mode - program accept only known requests.

Program using white-list strategy, it is more absolute protection, but its requires a lot of work on configuration.
In the program using Intellectual grafical UI	, its give an opportunity regularize most chaotic structure.

#### Contains libraries:<br>
Jquery-connections https://github.com/musclesoft/jquery-connections<br>
jQuery-1.11.3 https://jquery.com<br>
Google Charts https://developers.google.com/chart/<br>

#### Requires:<br>
Linux OS, Apache webserver with support htaccess and mod_rewrite,PHP5 with support CURL and MySQL<br>

##[How its working?](https://github.com/shaman33/web_app_firewall/wiki/How-its-working%3F)

##Getting Started	
###[Installation](https://github.com/shaman33/web_app_firewall/wiki/Installation-WebAppFirewall)
###[HTACCESS Injection](https://github.com/shaman33/web_app_firewall/wiki/HTACCESS-injection-via-WebAppFirewall)
###[Configuration Settings](https://github.com/shaman33/web_app_firewall/wiki/Configuration-Settings)
Set W.A.F Status Learn on, and Guard off.
Now program start collect request-map from every request to site, leave it for one week for view more complete structure of site.

###[Access Map - configuration permissions](https://github.com/shaman33/web_app_firewall/wiki/Configuration-Access-Map)
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

## Support 
Write me for help RomanShneer@gmail.com
