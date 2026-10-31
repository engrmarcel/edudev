# Backup and Restore

`database/sql/edudev.sql` is a **schema file, not a backup**. It contains no user data.

## Production backups (to set up at deployment)

- A nightly `mysqldump --single-transaction` of the full database, compressed and **encrypted** (for example `age` or `gpg`), copied off-server to object storage with a 30-day retention.
- Back up uploaded files (private object storage) with versioning turned on.
- Run a **restore test monthly** into a scratch database and record the result.
- Never keep backups in the web root or in Git.

## Restore outline

1. Put the site in maintenance mode: `php artisan down`.
2. Decrypt the dump and import it into a fresh database.
3. Point `.env` at the new database, then run `php artisan migrate --force`.
4. Run `php artisan up` and check `/health`.
