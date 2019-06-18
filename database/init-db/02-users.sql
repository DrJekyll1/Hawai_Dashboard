#############################
# CREATE NEW DATABASE USERS #
#############################


CREATE USER 'hawai'@'%' IDENTIFIED BY 'mysecret';
GRANT ALL ON `identity`.* TO 'hawai'@'%' ;

CREATE USER 'admin'@'%' IDENTIFIED BY 'fileserverSecret';
GRANT ALL ON `fileserver`.* TO 'admin'@'%' ;

FLUSH PRIVILEGES;