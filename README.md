# Corporate Lite

Multi-site Drupal project designed to run the following sites:

https://www.nisra.gov.uk/

https://afbini.gov.uk/

https://etini.gov.uk/

https://hseni.gov.uk/

https://northsouthministerialcouncil.org/

# Installation

At the command line, clone this repo, cd into it and then run the following:

```
lando start
lando composer install
maestro project:build
lando rebuild
```

NOTE Do not run 'maestro pub' in the corp lite project as it serves no purpose, there is no 'parent' repo
and any composer changes should be made in this repo.

