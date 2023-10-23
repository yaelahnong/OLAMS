--create user 
CREATE USER 'olamsUser'@'localhost' IDENTIFIED WITH authentication_plugin BY 'Pkl2023%'; -- ada issue dengan versi php tertentu

CREATE USER 'olamsUser'@'localhost' IDENTIFIED WITH mysql_native_password BY 'Pkl2023%'; -- untuk aplikasi php, ini recommended

--format syntaxnya
GRANT PRIVILEGE ON database.table TO 'username'@'host';

--buat dulu databasenya
CREATE DATABASE olams;

--memberi akses user mysql
GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT on olams.* TO 'olamsUser'@'localhost' WITH GRANT OPTION;

GRANT ALL PRIVILEGES ON *.* TO 'olamsSuper'@'localhost' WITH GRANT OPTION;

--setelah create user/update user privilegenya di flush
FLUSH PRIVILEGES;

buat nampilin privilegesnya pake
SHOW GRANTS FOR 'username'@'host';

https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql