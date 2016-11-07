EshtaFixturesBundle
===================
[![Build Status](https://travis-ci.org/eshta/fixtures-bundle.svg?branch=master)](https://travis-ci.org/eshta/fixtures-bundle)

Based on doctrine fixtures, similar to doctrine data fixtures bundle, but it persists fixtures instead, so it detects if a certain fixture is loaded it will not load it like doctrine migrations, best used as a place for seeded data.

Setup
-----
### Installation
```bash
composer require eshta/fixtures-bundle
```

```php
// app/AppKernel.php
// ...

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        // ...
        $bundles[] = new Eshta\FixturesBundle\EshtaFixturesBundle();

        return $bundles
    }

    // ...
}

```

### Configuration


Exclude the fixtures log table from DBAL schema
```yml
doctrine:
    dbal:
        schema_filter: ~^(?!fixtures_log)~
```

Usage
-----
#### Help:
```bash
app/console eshta:fixtures:load -h
```
#### Load:
```bash
app/console eshta:fixtures:load
```
It will load any outstanding fixtures only, it also supports ordering as with the normal fixtures bundle

#### Load file:
```bash
app/console eshta:fixtures:load [--force] <file>
```

Documentation
-------------
checkout [doctrine fixtures bundle](http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html) except for setup, the documentation is the same.
