
# Re:Machinon - server 
  
``` WARNING !! Do NOT make this repository public yet !!* ```   
## Work in progress  
  
ReMachinon is a web app created with Laravel framework that allows to register and connect to your Machinon devices remotely from anywhere in the world without any NAT, VPN or complicated router configurations.  
  
*Note: This software requires some knowledge of Apache and MySQL basic configuration*  
  
# Server requirements  
  
+ Apache 2.2+  
+ PHP 7.2  
+ MySQL 5.7  
+ SSHd  
+ Node.js (url)  
+ Composer (url)  
  
This software requires the use of Machinon devices with the following packets installed:  
  
+ Web Machinon - https://github.com/EdddieN/web_machinon
+ Agent Machinon - https://github.com/EdddieN/agent_machinon
  
You can get requirements, installation and setup details in each package's Github page.  
  
# ToDo  
  
- [ ] Visual styles  
- [ ] User groups  
- [ ] Global permissions  
- [ ] TLS on MQTT connections

# Done

- [X] User web authentication  
- [X] User API authentication  
- [X] Device registration  
- [X] Device tunnel connection and disconnection  
  
# Server setup  
   
#### Apache  
  
You need to setup a VirtualHost on the apache server that contains special dynamic reverse proxy tunneling directives.  
Use the Hostname, DocumentRoot that fit your needs  
The proxy file path directive must fit the installation DocumentRoot chosen.  
  
```  
TODO  
```  
  
#### MySQL  
Create a MySQL database and set an user and password for the app.  
  
#### SSHd  
  
You must setup a user account on the server to create the tunnels  
  
```  
TODO  
```   
  
# Installing  
  
Go to your Apache /var/www/htdocs or wherever you want to use as the VirtualHost DocumentRoot folder  
Ensure the folder is **empty**  
  
```  
git clone *url*  
cd remachinon  
composer update  
npm run production  
php artisan migrate  
>> php artisan *:cache etc commands here  
```  
  
# Updating  
  
```  
Code  
```  
  
# Usage  
  
- Go to the URL of your webserver  
- Login or register a user  
- Add a new device using your chosen name, the device's MUID (where is the MUID?) and a description.  
- Click on the Connect button and follow instructions.  
  
> Written with [StackEdit](https://stackedit.io/).
