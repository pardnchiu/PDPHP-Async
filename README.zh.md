# PD\Async

> PD\Async 是一個使用 ReactPHP 執行非同步任務的 PHP 函式庫，支援任務相依性處理和拓撲排序。

![tag](https://img.shields.io/badge/tag-PHP%20Library-bb4444)
![size](https://img.shields.io/github/size/pardnchiu/PHP-Async/src/Async.php)<br>
![version](https://img.shields.io/packagist/v/pardnchiu/async)
![download](https://img.shields.io/packagist/dm/pardnchiu/async)

## 特色功能

- 非同步任務執行
- 處理任務相依性
- 通過拓撲排序確保正確執行順序

## 核心功能

- 非阻塞任務處理
- 任務相依性管理
- 智慧執行排序
- Promise 錯誤處理
- 靈活的任務配置

## 相依套件

- `react/promise` - PHP 版本的 Promise/A+
- `react/event-loop` - PHP 事件循環函式庫

## 使用方式

### 安裝

```shell
composer require pardnchiu/async
```

```php
<?php
use PD\Async;

$tasks = [
    'task1' => [
        'method' => function() {
            return 'result1';
        },
        'tasks' => [],
    ],
    'task2' => [
        'method' => function() {
            return 'result2';
        },
        'tasks' => ['task1'], // 在 task1 之後執行
    ],
    'task3' => [
        'method' => function() {
            return 'result3';
        },
        'tasks' => ['task1'], // 在 task1 之後執行
    ],
    'task4' => [
        'method' => function() {
            return 'result3';
        },
        'tasks' => ['task2'], // 在 task2 之後執行
    ],
];

Async::run($tasks)
    ->then(function($results) {
        print_r($results);
    })
    ->catch(function($error) {
        echo 'Error: ' . $error->getMessage();
    });
```

## 授權條款

此原始碼專案採用 [MIT](https://github.com/pardnchiu/PHP-Async/blob/main/LICENSE) 授權。

## 作者

<img src="https://avatars.githubusercontent.com/u/25631760" align="left" width="96" height="96" style="margin-right: 0.5rem;">

#### 邱敬幃 Pardn Chiu

<a href="mailto:dev@pardn.io" target="_blank">
 <img src="https://pardn.io/image/email.svg" width="48" height="48">
</a>
<a href="https://linkedin.com/in/pardnchiu" target="_blank">
 <img src="https://pardn.io/image/linkedin.svg" width="48" height="48">
</a>

---

©️ 2024 [邱敬幃 Pardn Chiu](https://pardn.io)