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

2. Add the bundle to your application kernel:
```php
// app/AppKernel.php
public function registerBundles()
{
    // ...
    $bundle = array(
        // ...
        new NorvuTec\CronManagerBundle\NorvuTecCronManagerBundle(),
    );
    // ...

    return $bundle;
}
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
* * * * * /path/to/symfony/install/app/console cron-manager:run 1>> /dev/null 2>&1
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

Dependencies
------------------

CronManager requires ``symfony/lock`` to lock the job execution. 
If you want to use named locks, use cronmanager as keyword.