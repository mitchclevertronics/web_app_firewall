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

###Bad Requests
Logs of stopped attacks,  the logs recording only in <b>Guard Mode</b>. Its helpfully for learn structure and attack situations.

![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map9.jpg?raw=true)

## Support 
Write me for help RomanShneer@gmail.com
