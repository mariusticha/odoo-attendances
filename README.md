# odoo can do

**1. installation**

*complete installation*

`sudo bash setup.sh`

*alternatively: manually install prerequisites*

packages needed:

`sudo apt install php7.x-xml php7.x-gd php7.x-mbstring php7.x-zip`

where `x` is your current php version.

use composer to install PhpSpreadsheet into your project:

`composer require phpoffice/phpspreadsheet`

**2. run**

*run script*

`php odoocando.php --fname="Paul" --lname="Hammer" --start="2021-01-01" --end="2021-01-31"`
