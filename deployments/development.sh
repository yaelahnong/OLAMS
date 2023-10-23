#!/bin/bash
# ssh -i ~/.ssh/id_rsa root@38.47.180.137

# hapus folder zip
rm -rf zip

# buat baru folder zip
mkdir zip

# zip kodingan di local
# zip -r zip/development.zip *.php components/* images/* include/* javascripts/* styles/*
zip -r zip/development.zip *.php components/* images/* javascripts/* styles/*

# hapus zip kodingan yang ada di server
ssh -i ~/.ssh/id_rsa root@38.47.180.137 rm -rf /var/www/html/olams.ngobar.org/development.zip

# copy file zip kodingan ke server
scp -i ~/.ssh/id_rsa zip/development.zip root@38.47.180.137:/var/www/html/olams.ngobar.org

# unzip file zip kodingan di server
ssh -i ~/.ssh/id_rsa root@38.47.180.137 unzip -o /var/www/html/olams.ngobar.org/development.zip -d /var/www/html/olams.ngobar.org/