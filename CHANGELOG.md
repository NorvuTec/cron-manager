# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [8.1.2] - unreleased

### Fixed

- Job locks (`cronmanager:job:<tag>`) are now held for the full run lifetime.
  Previously `claimRunLock()` created a lock with `autoRelease: true` without
  storing the instance, so the lock was released as soon as the method returned.
  Parallel `cron-manager:run` processes could therefore select the same due job.
  The acquired `Lock` instance is kept until `releaseRunLock()`; `autoRelease` is
  `false`. On process crash the lock expires via TTL (default **300** seconds,
  configurable as `norvutec_cron_manager.run_lock_ttl`).

### Added

- Bundle config option `norvutec_cron_manager.run_lock_ttl` (float, seconds, min 1,
  default 300). Should be greater than the `cron-manager:run` process timeout (60s).
- Unit tests covering parallel claims, reclaim after release, and lock retention
  across `claimRunLock()` return.

## [8.1.1] - previous

- Prior release on `master` (see git history).
