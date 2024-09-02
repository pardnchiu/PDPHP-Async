# PDPHP\Async (PHP Library)

> PDPHP\Async is a PHP library using ReactPHP to perform asynchronous tasks, supporting task dependency handling and topological sorting.

## Features
- Asynchronous Task Execution
- Handling of Task Dependencies
- Ensuring correct execution order via Topological Sorting

## 創建者 / Creator

<img src="https://pardn.io/image/head-s.jpg" align="left" style="float: left; margin-right: 0.5rem; width: 96px; height: 96px;" />

<h4 style="padding-top: 0">邱敬幃 Pardn Chiu</h4>

[![](https://pardn.io/image/mail.svg)](mailto:mail@pardn.ltd) [![](https://skillicons.dev/icons?i=linkedin)](https://linkedin.com/in/pardnchiu) 

## License

This source code project is licensed under the [GPL-3.0](https://github.com/pardnchiu/PDMarkdownKit/blob/main/LICENSE) license.

## How to Use

- PHP version 7.4 or later
- Download `Async.php` and place it in the directory `src/PDPHP`
- Add dependencies in `composer.json`
    ```JSON
    {
        "require": {
            "react/promise": "^2.8 || ^3.0",
            "react/event-loop": "^1.1 || ^2.0"
        },
        "autoload": {
            "psr-4": {
                "PD\\": "src/PDPHP/"
            }
        }
    }
    ```
- Install dependencies
    ```SHELL
    composer install
    ```
- Example
    ```PHP
    <?php

    require 'vendor/autoload.php';

    use PDPHP\Async;

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
        })->catch(function ($error) {
            echo 'Error: ' . $error->getMessage();
        });
    ```

***

*All translations powered by ChatGPT*

***

©️ 2024 [邱敬幃 Pardn Chiu](https://www.linkedin.com/in/pardnchiu)