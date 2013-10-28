Generate MySQL records
======

PHP library for generating random records in MySQL databases.

Requirements
-----

`PHP` and the following extensions:
- `MySQL` or
- `MySQLi`

You can check if your system has the appropriate extensions with `php run.php --compatibilityTest`

If everything is ok, output should be something like this:

    $ php run.php --compatibilityTest
    Generate MySQL records v0.1 beta

    PHP Environment Compatibility Test (CLI)
    ----------------------------------------

    PHP 5.1 or newer............  Yes  5.3.6
    MySQL.......................  Yes
    MySQLi......................  Yes

    ----------------------------------------

    Your environment meets the minimum requirements.

Commandline Options
-----

In order to create records you have to run `php run.php` in `/src/library` folder.
You will see the following options:

    $ php run.php
    Generate MySQL records v0.1 beta

    Usage: php run.php [switches]

      --info                    Shows all possible commands.
      --compatibilityTest       Runs compatibility test.
      --fillRecords             Fills database tables with records.
      --setupConfig             Sets config.conf.php file.

First run `php run.php --setupConfig`

    $ php run.php --setupConfig
    Generate MySQL records v0.1 beta
    Enter MySQL hostname: 127.0.0.1
    Enter MySQL username: root
    Enter MySQL password:
    Enter MySQL schema: my_schema

Now you can run `php run.php --fillRecords`

    $ php run.php --fillRecords
    Generate MySQL records v0.1 beta

    Enter number of random records you would like to insert into following tables (press enter if you would like to skip table):
    - table1: 100
    - table2:
    - table3: 10000000
    - table4:
    - table5: 100000
    - table6: 40000
    Are you sure you would like to fill stated tables in database with random records? Type 'yes' if you do: yes

    Done filling tables with random data.
    ----------------------------------------
    Total time: 114 seconds, Memory: 10.50 MB
    Number of records inserted: 1140100
    ----------------------------------------

Optimization
-------

If you are inserting millions of records, you may need to tweak settings in `/src/config/config.conf.php` file.

```php
/**
 * Because texts can be very large, you may need to reduce it
 * Scripts generate text length between (1 - (DATAGENERATOR_*_MAX_SIZE / DATAGENERATOR_*_RATIO)
 */
$config['DATAGENERATOR_VARCHAR_RATIO'] = 1;
$config['DATAGENERATOR_TINYTEXT_RATIO'] = 10;
$config['DATAGENERATOR_TEXT_RATIO'] = 1000;
$config['DATAGENERATOR_MEDIUMTEXT_RATIO'] = 1000000;
$config['DATAGENERATOR_LONGTEXT_RATIO'] = 1000000000;
```

License
-------

Generate MySQL records is licensed under the MIT License