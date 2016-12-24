#!/bin/sh

echo Setting up virtual host

rm /etc/nginx/sites-available/default
cp /init/default-virtual-host /etc/nginx/sites-available/default

echo Setting up nginx root directory

chown -R www-data:www-data /root/workspace
