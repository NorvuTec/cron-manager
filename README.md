Cron Manager
===========

[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Installation
------------

Installing this bundle can be done through these simple steps:

1. Add the bundle to your project as a composer dependency:
```shell
composer require norvutec/cron-manager
```

2. Enable the bundle in your app:
```php
// config/bundles.php
return [
    // ...
    Norvutec\CronManagerBundle\NorvutecCronManagerBundle::class => ['all' => true],
];
```

3. Update your DB schema
```shell
bin/console make:migration
bin/console doctrine:migrations:migrate
```

4. Start using the bundle:
```shell
bin/console cron-manager:list
bin/console cron-manager:run
```

5. To run your cron jobs automatically, add the following line to your (or whomever's) crontab:
```
* * * * * /path/to/symfony/install/bin/console cron-manager:run 1>> /dev/null 2>&1
```

6. If you want to use the overview controller, create the file ``config/routes/norvutec_cronmanager.yaml`` with the following content:
```yaml
norvutec_cronmanager:
  resource: '@NorvutecCronManagerBundle/config/routes.yaml'
  prefix: '/cron-manager'
```
You now have access to the following routes:
* ``cronmanager_index`` : Overview of all jobs
* ``cronmanager_details`` : Details of a specific job

7. Optional: configure the job-lock TTL (default 300 seconds). The value should exceed the ``cron-manager:run`` process timeout (60s). On crash the lock expires after this TTL:
```yaml
# config/packages/norvutec_cron_manager.yaml
norvutec_cron_manager:
    run_lock_ttl: 300
```

Available commands
------------------

### list
```shell
bin/console cron-manager:list
```
Show a list of all jobs. Job names are show with ```[x]``` if they are enabled and ```[ ]``` otherwise.

### run
```shell
bin/console cron-manager:run [--force] [job]
```

Each invocation claims at most one job via a Symfony Lock ``cronmanager:job:<tag>``.
The lock is held until the run finishes (or the TTL expires after a crash), so parallel
``cron-manager:run`` processes can safely pick different due jobs.

Dependencies
------------------

CronManager targets Symfony ``^8.0`` and requires PHP ``>=8.4``.
It uses ``symfony/lock`` for execution locks.
If you want to use named locks, use cronmanager as keyword.
