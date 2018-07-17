#!/bin/bash

echo "update repository"
git pull
sudo chown www-data:www-data . -R
sudo chmod 777 . -R

echo "install composer packages"
composer install

echo "install node.js packages"
npm install

echo "compile assets"
npm run production

echo "change ownership and permissions"
sudo chown www-data:www-data . -R
sudo chmod 777 . -R

echo "Done."
