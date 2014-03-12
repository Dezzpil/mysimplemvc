mysimplemvc
===========

simple mvc for light projects


create .htaccess for Apache with :


AddDefaultCharset utf-8
RewriteEngine on
RewriteCond %{REQUEST_URI} !^/core/init.php
RewriteCond %{REQUEST_URI} !^.*[jpg|jpeg|gif|png|css|ico|doc|txt|html|htm]$
RewriteRule ^(.*)$ /core/init.php [L


set it in .conf server section for Nginx:

charset utf-8;
location = /core/init.php {}
location ~ .*[jpg|jpeg|gif|png|css|ico|doc|txt|html|htm]$ {}
location / {
  rewrite ^(.*)$ /core/init.php break;
}