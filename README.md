#Web App Firewall
##Introduction
WAFs goal is protect sites against hackers and virus attacks. 
Web App Firewall its PHP application that implement principle of reverse-proxy , control of types variables accepted by server , and comfortable management interface.<br>
![alt tag](https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/map0.jpg?raw=true)<br>
W.A.F. supported to work under LAMP servers with .htaccess files support.
Security protection based on white-list strategy: after starting "Learn" mode program collect map of requests, and user have to approve requests. After starting "Guard" mode - program accept only known requests.

Program using white-list strategy, it is more absolute protection, but its requires a lot of work on configuration.
In the program using Intellectual graphical UI	, its give an opportunity regularize most chaotic structure.

#### Contains libraries:<br>
Jquery-connections https://github.com/musclesoft/jquery-connections<br>
jQuery-1.11.3 https://jquery.com<br>
Google Charts https://developers.google.com/chart/<br>

#### Requires:<br>
Linux OS, Apache webserver with support htaccess and mod_rewrite,PHP5 with support CURL and MySQL<br>

##[How its working?](https://github.com/shaman33/web_app_firewall/wiki/How-its-working%3F)
Web App Firewall organize reverse-proxy by injection to .htaccess file, and writing Rewrite Rules with security key 1.

WAF script get redirected request and parse path and parameters sent from user. Detect created rules for specified situation and block or accept request via prepared politics.

If request approved, WAF script sending request  back to server via CURL with added security key 2 (.htaccess rule miss request if detect key2).
If request blocked, WAF save logs and show 404 page.
<img src="https://github.com/shaman33/web_app_firewall/blob/master/assets/imgs/scratch/reverse_proxy.png?raw=true">
##Getting Started	
###[Installation](https://github.com/shaman33/web_app_firewall/wiki/Installation-WebAppFirewall)
###[HTACCESS Injection](https://github.com/shaman33/web_app_firewall/wiki/Injection-to-.htaccess)
###[Configuration Settings](https://github.com/shaman33/web_app_firewall/wiki/Configuration-Settings)
Set W.A.F Status Learn on, and Guard off.
Now program start collect request-map from every request to site, leave it for one week for view more complete structure of site.
###[Access Map - configuration permissions](https://github.com/shaman33/web_app_firewall/wiki/Configuration-Access-Map)
###[Bad Requests log](https://github.com/shaman33/web_app_firewall/wiki/Bad-Requests)
###[Blacklist IP](https://github.com/shaman33/web_app_firewall/wiki/Blacklist)
## Support 
Write me for help RomanShneer@gmail.com<br>
Please donate:
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ECZBTKBD7T6A8"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif"></a>
