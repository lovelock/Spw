# Spw A Simple PDO Wrapper

## Features

1. MySQL 5.7.x JSON type support(partial).

## Prerequisites

1. PHP > 5.6
2. MySQL 5.7 for JSON type support

## Installation

`composer require lovelock/spw`

Then change `vender/lovelock/Spw/src/Config/Database.php` to adapt your own database configuration.

## Api

There are two kinds of methods, one is for querying conditions, the other is for executing queries.
 
### Querying conditions methods

1. `from($table)`
    Set table name of the query
   
1. `into($table)`
    Alias of `from($table)`
    
1. `where(array $conditions)`
    Set where clause of query.
    
1. `orderBy($col, $asc = 'desc')`
    Set sequence of result, this can be called multiple times for multiple orders.
    
1. `limit($limit)`
    Set limit number of result.
    
### Executing queries methods

1. `select($col)`
    Execute select query with specified columns.
    
1. `update($values)`
    Execute update query with specified columns and values.
    
1. `insert($values)`
    Execute insert query with specified columns and values.
    
1. `delete()`
    Execute delete query with specified conditions.
    

## Usage

You're highly recommended to use this library with DIC(Dependency Injection Container), thus Spw can be used as a service. Take a Slim application as example.

```php
<?php

// App initialization
$settings = [...];
$container = new \Slim\Container($settings);
$container['db'] = function ($c) {
    return new \Spw\Connection(new DevConfig());
};

$app = new \Slim\App($container);
$app->run();
.....

```


```php
<?php
class IoC
{
    private $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function __get($name)
    {
        return $this->c->get($name);
    }
}
```


```php
<?php

/**
 * @property ConnectionInterface db
 */
class ABC extends IoC
{
    public function foo()
    {
        $this->db->from('tablename')
        ->where(['id' => 1])
        ->select();
    }
}
```


## TODO

1. Cache system integration.
1. Decoupling of DIC.
1. Improve JSON support.

## Contribution

PR are welcome to improve this project.

## Donation

1. Alipay
    ![](http://ww4.sinaimg.cn/large/006y8mN6jw1fafuehixcmj30u019jtci.jpg)
2. WeChat
    ![](http://ww4.sinaimg.cn/large/006y8mN6jw1fafudylh28j30u00t9417.jpg)