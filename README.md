
Additional Vagrant Confing (cuz bad @ writing vagrant files) 
========================
1. install additional linux modules
    sudo apt-get install zip unzip
	sudo apt-get install php7.2-mysql php-xdebug php7.2-curl php7.2-xml php7.2-mbstring
2.  Add site to nginx sites-available:
        sudo pico /etc/nginx/sites-available/default
    change to:
        #root /var/www/html/web/;
         root /vagrant/web/;
2a. append following to sudo pico /etc/php/7.2/mods-available/xdebug.ini
    xdebug.max_nesting_level=150
    xdebug.remote_enable=true
    xdebug.remote_port=9000
    xdebug.remote_connect_back = on
    xdebug.idekey=PHPSTORM
    xdebug.remote_handler=dbgp
    xdebug.remote_mode=req
    xdebug.remote_host=localhost
3. reset MariaDb root password (to 'root') and configure remote host access.
      https://support.rackspace.com/how-to/mysql-resetting-a-lost-mysql-root-password/
      https://mariadb.com/kb/en/mariadb/configuring-mariadb-for-remote-client-access/
      
      GRANT ALL PRIVILEGES ON *.* TO 'root'@'192.168.100.%' IDENTIFIED BY 'root' WITH GRANT OPTION;

4. login to sql and create a database 
    mysql -u root -p
	CREATE DATABASE yipiao;
	exit
5. cd /vagrant run composer install
6. Run php bin/console doctrine:schema:update --force
7. run php bin/console fos:user:create adminuser --super-admin
RUn these to fill default data
7.1 php bin/console yipiao:solar-term:setup
7.2 run php bin/console yipiao:user-group:setup
8.1 current vagrant install has an active firewall. Exception needs to be added as per instructions in 3. if additional ports needs to be opened.
8.2 run "sudo chmod a+w var/cache -R" to fix cache clear issues

9. crontab setup https://github.com/J-Mose/CommandSchedulerBundle/blob/master/Resources/doc/index.md

10. setup email with certificates
    https://www.digitalocean.com/community/tutorials/how-to-configure-a-mail-server-using-postfix-dovecot-mysql-and-spamassassin#step-1-install-packages
    https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-apache-in-ubuntu-16-04
    http://blog.snapdragon.cc/2013/07/07/setting-postfix-to-encrypt-all-traffic-when-talking-to-other-mailservers/
    https://www.linuxbabe.com/mail-server/setting-up-dkim-and-spf
    run extra command after dkim guide
     - sudo chmod go-w / 

!!!if image upload in CYGWIN editor fails: 
root@yipiao-dev:/var/www/yipiao# cd web/
root@yipiao-dev:/var/www/yipiao/web# mkdir images
root@yipiao-dev:/var/www/yipiao/web# cd images
root@yipiao-dev:/var/www/yipiao/web/images# mkdir tea
root@yipiao-dev:/var/www/yipiao/web/images# cd ..
root@yipiao-dev:/var/www/yipiao/web# sudo chmod -R 777 images

!!!if teaFeature console command fails with doctrine error, solution is provided below
http://stackoverflow.com/questions/35670999/doctrine-querybuilder-order-by-clause-is-not-in-select-list

SOOS's
www.onesignal.com for push notifications.
www.algolia.com for search
https://typekit.com/account/kits for adobe webfonts
