# Installation:

website: https://github.com/brampta/barbavid
upload server: https://github.com/brampta/barbavid_uploadserver
video server: https://github.com/brampta/barbavid_videoserver

You need the website once but can have as many upload and video servers as you need.
They do not have to physically be on the same server.

# Requirements:

## for all servers:
-allowing files without extension with mod_negociation (at least just for .php files)
add AddType application/x-httpd-php .php to the /etc/apache2/mods-enabled/mime.conf file
-php extensions: curl

## for upload servers:
-uploadprogress_get_info pecl extension (pecl install uploadprogress)
-ffmpeg (macos brew install ffmpeg)
-php extensions: curl, gd, mbstring

# Configuration:

## main site:
-copy /settings.sample.php to /settings.php and adjust your settings
-cop /settings.custom.sample.php to /custom/settings.php 
-go to domain/dat_system_admin.php and reset the dat system
-use the table create queries from /create_tables.txt to create the database tables

## upload server: 
-create conf.php based on conf.sample.php
-note that upload server must have domains like upload1.maindomain.com, upload2.maindomain.com and so on..

## video server:
-create conf.php based on conf.sample.php
-domains must be like video1.maindomain.com, video2.maindomain.com, etc...

# Cronjobs:

## mains site cronjobs:
01 06 */6 * * /usr/bin/nice -n 19 php /var/www/barbavid/expire.php
13 */1 * * * /usr/bin/nice -n 19 php /var/www/barbavid/expire_key.php
30 5 * * * /usr/bin/nice -n 19 php /var/www/barbavid/bakup_dat_system.php
30 6 * * * /usr/bin/nice -n 19 php /var/www/barbavid/keep_only_x_baks.php

## upload server cronjobs:
*/3 * * * * /usr/bin/nice -n 19 php /var/www/barbavid/uploadserver1/encode.php

# Customization

The /custom folder is where you can customize your barbavid installation. This folder remains empty so you can create a git for your website in there with all your customizations.

There are 2 customization methods available.

## Decidated file customizations

Simply create files with specific names in the /custom/ directory and they will be run by barbavid. This should cover most basic configurations you will need to do.

* /custom/footer.php: runs in the footer area
* /custom/404.php: will be shown when user requests a page that does not exist
* /custom/custom.css: will be loaded after the barbavid CSS so you can add you own CSS
* /custom/web/: you can create a folder named web/ in the /custom/ folder. Anything in this folder will be accessible online via the path yoursite.com/custom/. For example if you put an image at /custom/web/image.jpg, it will be accessible online at yousite.com/custom/image.jpg. This is where you need to add your images, CSS or JavaScript files. You can also use the /custom/web folder to add paths to the website. For example if you create the file /custom/web/mypage.php you will be able to access it at domain.com/mypage

## Modules (coming soon...)

To allow more complex and better structured modifications you can also create modules. For that you need to create the modules/ folder inside of the /custom/ folder.

Then every folder directly inside of /custom/modules/ that contains an init.php file will be considered a module and the init.php will run at barbavid initialization.

!! change of plan !!

Example: /custom/modules/example_module/init.php

You can put any code that you want in the init.php but the idea is to attach file includes to events already planned in barbavid.

For exemple if you write:

event_listen('footer','folder1/footer_tasks.php',100);

in your module, this means that when the footer event is triggered the file located at folder1/footer_tasks.php in your module will run. That means the file will really be at /custom/modules/example_module/folder1/footer_tasks.php. The 3rd parameter is a priority so if there are mutliple listeneres attached to a same event they will run in the order specified.

!! new plan.. !!

You can create a /web folder inside of your module (lie this: /custom/modules/mymodule/web) which will act in the same way as the /custom/web folder. However in a case were both override the same file, the /custom/web folder will have higher priority

You can also create an /include folder inside of your module and inside of it create a /before, /æfter and override folder (like this: /custom/modules/mymodule/include/before, /custom/modules/mymodule/include/after and /custom/modules/mymodule/include/override).
Every file that is included in barbavid with the smart_include function will look for these in each module.

Files in the override folder will override an include file of the same name. Files in the before and after folders will be included before and after an include of a file of a same name...

For example, if you create the file /custom/modules/mymodule/include/before/page_end.php, it will be included before /include/page_end.php is included in a barbavid page template.

...Now include before, after and override is a cool feature, but there should be methods of extending a class and modifying just 1 method or something instead of forcing people to override the whole class. or should we just put events in the classes for that? maybe like events everywhere...??