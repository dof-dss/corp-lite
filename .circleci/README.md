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

## Manual Edge trial

In CircleCI, trigger this application's `CORP-855` branch with
`shared_ci_ref=CORP-855`, `ci_action=edge-build`, `source_branch=development`
and boolean `edge_publish=false`. Review the `edge-build` job artifacts.
Another published source branch can be selected per trigger.

`source_branch` and `edge_publish` belong in setup/trigger parameters, not
`parameters.json`. Publication defaults to false; real publishing schedules
must explicitly pass `edge_publish=true`.
