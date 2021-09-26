# Installation:

website: https://github.com/brampta/barbavid
upload server: https://github.com/brampta/barbavid_uploadserver
video server: https://github.com/brampta/barbavid_videoserver

You need only one website but you can have as many upload and video servers as you need.

They do not have to physically be on the same server. However there is not yet any built-in support to have upload servers and video servers separated from each other, but it can be done with networked folders or other.

For now to supported architechture is: 1 website, many upload+video servers.

# Requirements:

## for all servers:
-allowing files without extension with mod_negociation (at least just for .php files)
add AddType application/x-httpd-php .php to the /etc/apache2/mods-enabled/mime.conf file and set the Option MultiViews in your virtual host config
-php extensions: curl

## for upload servers:
-uploadprogress_get_info pecl extension (pecl install uploadprogress)
-ffmpeg (macos brew install ffmpeg, ubuntu sudo apt install ffmpeg)
-php extensions: curl, gd, mbstring

# Configuration:

## main site:
-copy /settings.sample.php to /settings.php and adjust your settings
-copy /settings.custom.sample.php to /custom/settings.php [I think this step needs to be removed..]
-go to domain/dat_system_admin.php and reset the dat system
-use the table create queries from /create_tables.txt to create the database tables
-make sure the web server is able to write in the dat_system folder and all of its subfolders

## upload server: 
-create conf.php based on conf.sample.php
-note that upload server must have domains like upload1.maindomain.com, upload2.maindomain.com and so on..
-make sure the web server is able to write in the uploads, encoding_queue, encoding_queue_errors and encoding_queue_inprogress folders

## video server:
-create conf.php based on conf.sample.php
-domains must be like video1.maindomain.com, video2.maindomain.com, etc...
-make sure the web server is able to write in the videos folder

# Cronjobs:

## mains site cronjobs:
01 06 */6 * * /usr/bin/nice -n 19 php [path to barbasite]/web/cron/expire.php
13 */1 * * * /usr/bin/nice -n 19 php [path to barbasite]/web/cron/expire_key.php
30 5 * * * /usr/bin/nice -n 19 php [path to barbasite]/web/dat_system/bakup_dat_system.php
30 6 * * * /usr/bin/nice -n 19 php [path to barbasite]/web/dat_system/keep_only_x_baks.php

## upload server cronjobs:
* * * * * /usr/bin/nice -n 19 php [path to upload server]/web/cron/encode.php

# Customization
To allow you to have ome git repo for barbavid and another for your own code, you can use the $customization_folder settings.php variable to point barbavid to your own customization folder, which can be outside of the barbavid git repo so it can have its own git. You dont need to have a customization folder.

There are 2 customization methods available.

## Decidated file customizations

Simply create files with specific names in the customization folder directory and they will be run by barbavid. This should cover most basic configurations you will need to do.

* /footer.php: runs in the footer area
* /404.php: will be shown when user requests a page that does not exist
* /custom.css: will be loaded after the barbavid CSS so you can add you own CSS
* /web/: you can create a folder named web/ in the customization folder. Anything in this folder will be accessible online via the path yoursite.com/fileinweb. For example if you put an image at /web/image.jpg in the customization folder, it will be accessible online at yousite.com/image.jpg. This is a place where you can add images, CSS or JavaScript files. You can also use the /web folder to add paths (or pages) to the website. For example if you create the file /web/mypage.php in the customization folder you will be able to access it at domain.com/mypage

exemple file in /web, /web/info.php, makes an info page at yoursite.com/info:

```php
<?php
include dirname(dirname(__FILE__)).'/settings.php'; //this loads barbavids settings, including the $barbavid_folder variable, you need that
include $barbavid_folder.'/include/init.php'; //this instanciates the barbastuff, now barbavid is ready to barbrock!

$page_title=$text[2009];
include(BP.'/include/head_start.php'); //head open! (ouch)
include(BP.'/include/head_end.php'); //head close (you can put stuff between!)
include(BP.'/include/header.php'); //actual nav header bar

//your code goes here. you can use the barbafunctions...
//...............

include(BP.'/include/footer.php'); //thats your footer if you have one
include(BP.'/include/page_end.php'); //and thats the page end (more usefulness for page end coming soon probably!...)
```

## Modules (coming soon...)

To allow more complex and better structured modifications you can also create modules. For that you need to create the modules/ folder inside of the customization folder.

Then every folder directly inside of /modules/ that contains an init.php file will be considered a module and the init.php will run at barbavid initialization.

!! change of plan !!

Example: /modules/example_module/init.php

You can put any code that you want in the init.php but the idea is to attach file includes to events already planned in barbavid.

For exemple if you write:

event_listen('footer','folder1/footer_tasks.php',100);

in your module, this means that when the footer event is triggered the file located at folder1/footer_tasks.php in your module will run. That means the file will really be at /modules/example_module/folder1/footer_tasks.php in your customization folder. The 3rd parameter is a priority so if there are mutliple listeneres attached to a same event they will run in the order specified.

!! new plan.. !!

You can create a /web folder inside of your module (lie this: /modules/mymodule/web) which will act in the same way as the /web folder. However in a case were both override the same file, the /custom/web folder will have higher priority

You can also create an /include folder inside of your module and inside of it create a /before, /after and override folder (like this: /modules/mymodule/include/before, /modules/mymodule/include/after and /modules/mymodule/include/override).
Every file that is included in barbavid with the smart_include function will look for these in each module.

Files in the override folder will override an include file of the same name. Files in the before and after folders will be included before and after an include of a file of a same name...

For example, if you create the file /custom/modules/mymodule/include/before/page_end.php, it will be included before /include/page_end.php is included in a barbavid page template.

...Now include before, after and override is a cool feature, but there should be methods of extending a class and modifying just 1 method or something instead of forcing people to override the whole class. or should we just put events in the classes for that? maybe like events everywhere...??

To be reflected on!

