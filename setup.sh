#!/bin/bash

echo "reading php version"
start=16;stop=18;
php -i | grep "PHP Version =>" | cut -c $start-$stop | head -1; >> $version

echo -e "\e[32m $version"
echo  " - found php version"
echo ""
echo -e " - installing packages\e[0m"
sudo apt install php$version-xml php$version-gd php$version-mbstring php$version-zip
echo ""
echo -e "\e[32m - installing phpspreadsheet\e[0m"
composer require phpoffice/phpspreadsheet