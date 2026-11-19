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

## Multisite Edge refresh after deployment

The shared refresh processes the explicit `sites` list from `parameters.json`:
nisra, afbini, hseni, etini and northsouthministerialcouncil. It checks the target,
copies the parent environment's database and files, then runs these steps for
**each site**, failing on a failed bootstrap or command:

1. Bootstrap Drupal and run database updates.
2. Rebuild caches and import configuration.
3. If Structure Sync is enabled, import blocks and taxonomies using safe mode.
4. Enable QA accounts, reset their passwords and rebuild caches.

This Drupal 10 branch uses standard updates without `upgrade-preflight`, retains
Fastly configuration and does not create extra QA staff accounts. These are
explicit settings rather than Unity-specific assumptions.

To run without a scheduler, trigger a **new pipeline** on this application's
`CORP-855` branch using `shared_ci_ref=CORP-855` and `ci_action=edge-finalise`.
This selects workflow `edge-finalise` and job `edge_finalise_shared`.
`edge_publish` has no effect on refresh; setting it false does not prevent
remote data changes. `source_branch` controls builds only; refresh uses whatever
code is currently deployed on Edge.

Before triggering, confirm the intended Edge code has completed its Upsun
deployment and Edge's parent is the intended data source. CircleCI must have
`PLATFORM_PROJECT` and the project's existing Platform CLI authentication/SSH
access. The deployed environment needs `QA_PASSWORD` and the Origins QA commands.
Deployment hooks still run normally; refresh reapplies configuration after data sync.

The two manual runs are independent: `edge-build` with `edge_publish=true`
publishes code, then after deployment `edge-finalise` replaces Edge data and
finalises it. An `edge_publish=false` artifact build has not deployed new code.
There is no automatic dependency or deployment-completion check between them.
Only trigger refresh when replacing Edge's current databases/files is intended.
