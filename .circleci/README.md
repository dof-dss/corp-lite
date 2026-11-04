# Shared application CI.

Project parameters live in `parameters.json`; setup selects the shared branch
and action in `config.yml`.

For local deprecation checks using the same runner and policy as CI:

```bash
ddev phpstan web/modules/origins
ddev phpstan web/modules/custom web/modules/origins web/themes/custom
```

The host command reads the sibling `webdev-ci/scripts/phpstan/deprecations.sh`.
Set `WEBDEV_CI_DIR` for another checkout. Run Composer install using this branch's
lockfile before checking. Hosted CI uses the copy published on its selected
shared branch, so publish shared changes before comparing results.
This command matches `deprecated_code_phpstan`; disallowed-function checks
use the separate `.circleci/phpstan.neon` policy.
