# Corporate Lite

Multi-site Drupal project designed to run the following sites:

https://www.nisra.gov.uk/

https://afbini.gov.uk/

https://etini.gov.uk/

https://hseni.gov.uk/

https://northsouthministerialcouncil.org/

# Installation

At the command line, clone this repo, cd into it and then run the following:

lando start

lando composer install

lando restart

Create a .env file in the root directory by copying .env.sample.

Install Drupal using the database credentials from 'lando info' (as well as setting the database name, username and password,
remember to change the database host to 'database' under 'Advanced options')
