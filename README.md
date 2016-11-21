# Spw A Simple PDO Wrapper

## Features

1. MySQL 5.7.x support
2. JSON type support

## Installation

`composer require lovelock/spw`

Then change `vender/lovelock/Spw/src/Config/Database.php` to adapt your own database configuration.

## Api

1. `table`
1. `select`
    
    ```
    <?php
    
    ...->select('col1', 'col2');
    ...->select(['col1', 'col2']);
    ```
    
1. `where`

    ```
    <?php
    
    ...->where(['col1' => 'val1']);
    ...->where(['col1' => [
                'EQ' => 'val1',
                'NEQ' => 'val2',
                ]]);
    ```
    
    Available comparison operators:
    ```
    'EQ' => '=',
    'NEQ' => '<>',
    'GT' => '>',
    'EGT' => '>=',
    'LT' => '<',
    'LIKE' => 'LIKE',
    'IN' => 'IN',
    'JSON_CONTAINS' => 'JSON_CONTAINS',
    'JSON_SEARCH' => 'JSON_SEARCH',
     ```
    
1. `orderBy`
1. `limit`
1. `get`
1. `first`
1. `value`
1. `count`
1. `get`
1. `insert`
1. `update`
1. `delete`

## Contribute

