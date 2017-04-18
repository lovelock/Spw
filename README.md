# Spw A Simple PDO Wrapper

## Features

1. MySQL 5.7.x JSON type support(partial).

## Prerequisites

1. PHP > 5.6
2. MySQL 5.7 for JSON type support

## Installation

`composer require lovelock/spw`

The you implement ConfigInterface to provide your Database configuration. Config files like ini/yaml/xml or any other formats are OK only if you implement the interface.

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
    
1. `getNumRows()`
    Get number of rows with specified conditions.
    
1. `groupBy()`
    Set group by clause.
    
> `where()` method accepts an array as parameter. The array can be various formats.
1. `where(['id' => 20])` means `where id = 20`
2. `where(['id' => ['IN', [2, 3, 4]]])` means `where id in (2, 3, 4)`
3. `where(['id' => ['NOT IN', [2, 3, 4]]])` means `where id not in (2, 3, 4)`
4. `where(['id' => ['IS', 'NULL']])` means `where id is null`
5. `where(['id' => ['IS', 'NOT NULL']])` means `where id is not null`
6. `where(['id' => ['BETWEEN', [2, 4]]])` means `where id between (2, 4)`
    
### Executing queries methods

1. `select($col)`  
    Execute select query with specified columns.
    
1. `update($values)`  
    Execute update query with specified columns and values.
    
1. `insert($values)`  
    Execute insert query with specified columns and values.
    
1. `delete()`  
    Execute delete query with specified conditions.
    
1. `replace()`
    Execute replace query with specified data.

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

1. WeChat  
    ![](http://ww3.sinaimg.cn/small/006y8mN6jw1fafuqzir1ej30g20mr76a.jpg)
2. Alipay  
    ![](http://ww1.sinaimg.cn/small/006y8mN6jw1fafurfgkg0j30gn0ml76m.jpg)
