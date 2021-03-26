requirements:

for all servers:
-allowing files without extension with mod_negociation (at least just for .php files)
add AddType application/x-httpd-php .php to the /etc/apache2/mods-enabled/mime.conf file

for upload servers:
-uploadprogress_get_info pecl extension (pecl install uploadprogress)
-ffmpeg (macos brew install ffmpeg)

Installation:

main site:
-create settings.php based on settings.sample.php
-go to domain/dat_system_admin.php and reset the dat system

upload server: 
-create conf.php based on conf.sample.php

Cronjobs:

mains site cronjobs:
01 06 */6 * * /usr/bin/nice -n 19 php /var/www/barbavid/expire.php
13 */1 * * * /usr/bin/nice -n 19 php /var/www/barbavid/expire_key.php
30 5 * * * /usr/bin/nice -n 19 php /var/www/barbavid/bakup_dat_system.php
30 6 * * * /usr/bin/nice -n 19 php /var/www/barbavid/keep_only_x_baks.php

upload server cronjobs:
*/3 * * * * /usr/bin/nice -n 19 php /var/www/barbavid/uploadserver1/encode.php


