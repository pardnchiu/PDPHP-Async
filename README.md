# PD\Async

> PD\Async is a PHP library using ReactPHP to perform asynchronous tasks, supporting task dependency handling and topological sorting.

![tag](https://img.shields.io/badge/tag-PHP%20Library-bb4444) 
![size](https://img.shields.io/github/size/pardnchiu/PHP-Async/src/Async.php)<br>
![version](https://img.shields.io/packagist/v/pardnchiu/async)
![download](https://img.shields.io/packagist/dm/pardnchiu/async)

## Features
- Asynchronous Task Execution
- Handling of Task Dependencies
- Ensuring correct execution order via Topological Sorting

## Key Capabilities

- Non-blocking Task Processing
- Task Dependency Management
- Smart Execution Ordering
- Promise Error Handling
- Flexible Task Configuration

## Dependencies

- `react/promise` - Promise/A+ for PHP
- `react/event-loop` - Event Loop Library for PHP

## How to Use

### Install

```SHELL
composer require pardnchiu/async
```

```php
<?php

use PD\Async;

$tasks = [
    'task1' => [
        'method' => function () {
            return 'result1';
        },
        'tasks' => [],
    ],
    'task2' => [
        'method' => function () {
            return 'result2';
        },
        'tasks' => ['task1'], // Run after task1
    ],
    'task3' => [
        'method' => function () {
            return 'result3';
        },
        'tasks' => ['task1'], // Run after task1
    ],
    'task4' => [
        'method' => function () {
            return 'result3';
        },
        'tasks' => ['task2'], // Run after task2
    ],
];

Async::run($tasks)
    ->then(function ($results) {
        print_r($results);
    })
    ->catch(function ($error) {
        echo 'Error: ' . $error->getMessage();
    });
```

## License

This source code project is licensed under the [MIT](https://github.com/pardnchiu/PHP-Async/blob/main/LICENSE) license.

## Creator

<img src="https://avatars.githubusercontent.com/u/25631760" align="left" width="96" height="96" style="margin-right: 0.5rem;">

<h4 style="padding-top: 0">邱敬幃 Pardn Chiu</h4>

<a href="mailto:dev@pardn.io" target="_blank">
    <img src="https://pardn.io/image/email.svg" width="48" height="48">
</a> <a href="https://linkedin.com/in/pardnchiu" target="_blank">
    <img src="https://pardn.io/image/linkedin.svg" width="48" height="48">
</a>

***

©️ 2024 [邱敬幃 Pardn Chiu](https://pardn.io)