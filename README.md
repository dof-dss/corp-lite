# Corporate Lite

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/dof-dss/corp-lite/tree/development.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/dof-dss/corp-lite/tree/development)

Multi-site  Drupal project designed to run the following sites:

- https://www.nisra.gov.uk/
- https://afbini.gov.uk/
- https://etini.gov.uk/
- https://hseni.gov.uk/
- https://northsouthministerialcouncil.org/

# Installation

## Prerequisites

- Access to the hosting platform (for access to database resources)
- GitHub account with relevant permissions
- Platform.sh CLI tool: https://docs.platform.sh/administration/cli.html#1-install
- We recommend Lando for local development:  https://docs.devwithlando.io/

At the command line, clone this repo, cd into it and then run the following:

```
lando start
maestro project:build
lando rebuild
```

> NOTE Do not run 'maestro pub' in the corp lite project as it serves no purpose, there is no 'parent' repo
and any composer changes should be made in this repo.

For each site (e.g. nisra)
- Import the db: `lando db-import nisra <filename>.sql.gz`
- Rebuild the app container and import local config split settings: `lando drush cr -l nisra && lando cim -y -l nisra`

# Code workflow

Like the popular git-flow workflow, but without the more complex elements:

- `development` bleeding-edge. All feature branches originate from here.
- `main` stable, automatically deployed to platform.sh. Release tags should originate from here.

API keys, auth tokens or other sensitive values must be stored as environment variables and never stored in the codebase itself.

## Continuous integration

Automated testing is configured to check:

- Validity of the build based on the composer files.
- Static analysis of custom PHP code against drupal.org coding standards using [phpcs](https://github.com/squizlabs/PHP_CodeSniffer).
- Analysis of custom code for deprecated code using [drupal-check](https://github.com/mglaman/drupal-check).
- Run any defined unit tests via [phpunit](https://phpunit.de/).

Most of these tasks can be run locally with [Circle CI's CLI tool](https://circleci.com/docs/local-cli/).

## Release naming conventions

The project operates using [semantic versioning](https://semver.org/).

# Contribution

> Contributors to repositories hosted in dof-dss are expected to follow the Contributor Covenant Code of Conduct, and those working within Government are also expected to follow the Northern Civil Service Code of Ethics and Civil Service Code. For details see https://github.com/dof-dss/contributor-code-of-conduct

All changes should be submitted with an appropriate pull request (PR) in GitHub. Direct commits to `main` or `development` are not normally permitted.

# Licence
Unless stated otherwise, the codebase is released under [the MIT License](http://www.opensource.org/licenses/mit-license.php). This covers both the codebase and any sample code in the documentation.
