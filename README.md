Charcoal FooBar
===============

[![License][badge-license]][charcoal-contrib-notification]
[![Latest Stable Version][badge-version]][charcoal-contrib-notification]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

Admin notification service.



## Table of Contents

-   [Installation](#installation)
    -   [Dependencies](#dependencies)
-   [Service Provider](#service-provider)
    -   [Parameters](#parameters)
    -   [Services](#services)
-   [Configuration](#configuration)
-   [Usage](#usage)
-   [Development](#development)
    -  [API Documentation](#api-documentation)
    -  [Development Dependencies](#development-dependencies)
    -  [Coding Style](#coding-style)
-   [Credits](#credits)
-   [License](#license)



## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotivemtl/charcoal-contrib-notification
```



### Dependencies

#### Required

-   [**PHP 5.6+**](https://php.net): _PHP 7_ is recommended.
-   [**locomotivemtl/charcoal-admin**](https://github.com/locomotivemtl/charcoal-admin) >=0.15.7



#### PSR

-   [**PSR-7**][psr-7]: Common interface for HTTP messages. Fulfilled by Slim.
-   [**PSR-11**][psr-11]: Common interface for dependency containers. Fulfilled by Pimple.


## Configuration

In your project's config file, require the notification module : 
```json
{
    "modules": {
        "charcoal/notification/notification": {}
    }
}
```

## Usage

Define notifiable objects with `object/collection?obj_type=charcoal/notification/notification-target`

Setup your notifications via `object/collection?obj_type=charcoal/notification/notification`

Set the cron jobs as follow:

```
// Daily (8 stands for 8am)
0 8 * * * cd /[project]/web && /usr/local/bin/php /[project]/web/vendor/bin/charcoal admin/notification/daily

// Hourly
0 * * * * cd /[project]/web && /usr/local/bin/php /[project]/web/vendor/bin/charcoal admin/notification/hourly

// Every minute
* * * * * cd /[project]/web && /usr/local/bin/php /[project]/web/vendor/bin/charcoal admin/notification/minute

// Monthly (8 stands for 8am)
0 8 1 * * cd /[project]/web && /usr/local/bin/php /[project]/web/vendor/bin/charcoal admin/notification/montly

// Weekly (8 stands for 8am, 1 stands for monday)
0 8 * * 1 cd /[project]/web && /usr/local/bin/php /[project]/web/vendor/bin/charcoal admin/notification/weekly
```


## Development

To install the development environment:

```shell
$ composer install
```

To run the scripts (phplint, phpcs, and phpunit):

```shell
$ composer test
```


### API Documentation

-   The auto-generated `phpDocumentor` API documentation is available at:  
    [https://locomotivemtl.github.io/charcoal-contrib-notification/docs/master/](https://locomotivemtl.github.io/charcoal-contrib-notification/docs/master/)
-   The auto-generated `apigen` API documentation is available at:  
    [https://codedoc.pub/locomotivemtl/charcoal-contrib-notification/master/](https://codedoc.pub/locomotivemtl/charcoal-contrib-notification/master/index.html)



### Development Dependencies

-   [php-coveralls/php-coveralls][phpcov]
-   [phpunit/phpunit][phpunit]
-   [squizlabs/php_codesniffer][phpcs]



### Coding Style

The charcoal-contrib-notification module follows the Charcoal coding-style:

-   [_PSR-1_][psr-1]
-   [_PSR-2_][psr-2]
-   [_PSR-4_][psr-4], autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   [phpcs.xml.dist](phpcs.xml.dist) and [.editorconfig](.editorconfig) for coding standards.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.



## Credits

-   [Locomotive](https://locomotive.ca/)



## License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.

[charcoal-contrib-notification]:  https://packagist.org/packages/locomotivemtl/charcoal-contrib-notification
[charcoal-app]:             https://packagist.org/packages/locomotivemtl/charcoal-app

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-contrib-notification/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-contrib-notification
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-contrib-notification

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-contrib-notification.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-contrib-notification.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-contrib-notification.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-contrib-notification.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-contrib-notification.svg?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
