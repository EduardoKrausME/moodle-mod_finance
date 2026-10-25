# Changelog

This changelog documents **only the two most recent published GitHub releases** of `mod_finance`. Each entry is based on comparing its published release tag with the immediately preceding **published release** tag, rather than assuming every intervening version-named commit has a published release. Commits after `1.1.5` are excluded.

## [1.1.5] - 2026-10-15

Published: 2026-10-15 21:18:06 UTC.

Comparison: [1.1.3...1.1.5](https://github.com/EduardoKrausME/moodle-mod_finance/compare/1.1.3...1.1.5)

### Continuous integration

- Updated the PostgreSQL CI service image from `postgres:16` to **`postgres:17`**.
- Updated the MariaDB CI service image from `mariadb:10` to **`mariadb:11.4`**.
- Changed the MariaDB empty-password configuration to `MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1`, replacing the `mysqladmin ping` readiness check with `healthcheck.sh --connect --innodb_initialized` and adjusting the retry count.

### Plugin assets

- Added the PNG plugin icon `pix/icon.png`.
- Renamed `pix/icon.svg` to `pix/monologo.svg` without changes to the SVG content in the published-tag comparison.

### Version metadata and scope

- Updated `version.php` from numeric version `2026101200` / release `1.1.3` to `2026101501` / release `1.1.5`. The minimum Moodle build remains `2024100700`.
- The comparison contains six commits, including an intervening commit titled `V 1.1.4`. **Version `1.1.4` is not a separately published GitHub release in the release list examined**; the immediate preceding published release of `1.1.5` is `1.1.3`.
- The net changes are limited to `.github/workflows/ci.yml`, `pix/icon.png`, the SVG rename and `version.php`. No finance activity runtime source or calculation behavior changed. CI database image upgrades do not, by themselves, establish new production database requirements.

## [1.1.3] - 2026-10-12

Published: 2026-10-12 11:29:12 UTC.

Comparison: [1.1.2...1.1.3](https://github.com/EduardoKrausME/moodle-mod_finance/compare/1.1.2...1.1.3)

### Continuous integration and compatibility testing

- Updated the PHP 8.3 test matrix entry from `MOODLE_502_STABLE` to **`MOODLE_503_STABLE` (Moodle 5.3)**.

### Release workflow security

- Changed `.github/workflows/release.yml` to pass the commit message into the version-parsing shell script through a `RELEASE_MESSAGE` environment variable instead of interpolating commit text directly into shell code, reducing shell-injection exposure.
- Normalized the release workflow file's final newline.

### Release metadata and scope

- Updated `version.php` from numeric version `2026100500` / release `1.1.2` to `2026101200` / release `1.1.3`. The minimum Moodle build remains `2024100700`.
- The three-commit comparison changes only `.github/workflows/ci.yml`, `.github/workflows/release.yml` and `version.php`. No finance activity functional code, calculations or UI assets changed between these published tags.

---

Entries reflect verified differences between published release tags. No tests or deployments were run, and no release descriptions were changed, while preparing this changelog.
