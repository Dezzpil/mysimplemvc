mysimplemvc
===========

simple mvc for light projects

create .htaccess with :
AddDefaultCharset utf-8
RewriteEngine on
RewriteCond %{REQUEST_URI} !^/core/init.php
RewriteCond %{REQUEST_URI} !^.*[jpg|jpeg|gif|png|css|ico|doc|txt|html|htm]$
RewriteRule ^(.*)$ /core/init.php [L]