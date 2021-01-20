## Git branch workflow

### How work with git branches:

- Create new branches form `develeopment`
- Name template for branch: dev/{task_id}-short-text. Add branch name to task as comment & "Pin to top"
- * Pull from `develeopment` if your branch hasn't been just created
- Merge(pull request) your branch to `test` branch
- Commit message template: `{task_id} {text}`. Only latin letters allowed
- After testing merge(pull request) branch to `stage`
- After checked on `stage`, merge(only pull request allowed) branch to `master` or merge(only pull request allowed) all `stage` to `master`
- At last, merge(pull request) `master` to `develeopment`
- After task is tested and branch merged into `master`, then branch should be deleted