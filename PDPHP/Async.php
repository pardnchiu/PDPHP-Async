<?php

namespace PDPHP;

use React\Promise;
use React\EventLoop\Loop;

class Async
{
    // 執行異步任務
    public static function run($tasks)
    {
        // 獲取事件循環實例
        $loop = Loop::get();

        // 初始化任務流程和方法數組
        $flow = [];
        $methods = [];

        // 遍歷所有的任務
        foreach ($tasks as $taskKey => $task) {
            // 提取每個任務的方法
            $methods[$taskKey] = $task['method'];

            // 如果該任務沒有依賴的任務，設置為空數組；否則，保存依賴的任務
            if (empty($task['tasks'])) {
                $flow[$taskKey] = [];
            } else {
                $flow[$taskKey] = $task['tasks'];
            };
        };

        // 對任務流程進行拓撲排序，確保任務按正確的順序執行
        $sortedFlow = self::topologicalSort($flow);

        // 創建所有的任務，並根據排序好的流程和查詢參數
        $tasks = self::createTasks($methods, $flow, $sortedFlow, $loop);

        // 使用 Promise.all 等待所有任務完成
        return Promise\all($tasks)
            ->then(function ($results) use ($loop) {
                // 當所有任務完成後，運行事件循環
                $loop->run();
                // 返回所有任務的結果
                return $results;
            })
            ->catch(function ($error) {
                // 捕獲並拋出異常
                throw $error;
            });
    }

    // 排序任務流程，使用拓撲排序算法
    private static function topologicalSort($flow)
    {
        $sorted = [];  // 已排序的任務
        $visited = []; // 已訪問的任務
        $temporary = []; // 臨時訪問的任務，用於檢測循環依賴

        // 遍歷所有任務
        foreach ($flow as $taskKey => $tasks) {
            // 對每個任務進行訪問
            if (is_int($taskKey)) {
                $taskKey = $tasks;
                $tasks = [];
            };

            if (!isset($visited[$taskKey])) {
                self::visit($taskKey, $flow, $visited, $sorted, $temporary);
            };
        };

        // 返回反轉後的排序結果，確保正確的順序
        return array_reverse($sorted);
    }

    private static function visit($taskKey, $flow, &$visited, &$sorted, &$temporary)
    {
        // 如果在臨時數組中找到該任務，說明存在循環依賴
        if (isset($temporary[$taskKey])) {
            throw new \Exception("Circular dependency detected: " . $taskKey);
        };

        // 如果該任務還沒有被訪問過
        if (!isset($visited[$taskKey])) {
            $temporary[$taskKey] = true; // 標記為臨時訪問

            // 遍歷該任務的所有依賴任務
            if (isset($flow[$taskKey])) {
                foreach ($flow[$taskKey] as $task) {
                    self::visit($task, $flow, $visited, $sorted, $temporary);
                };
            };

            unset($temporary[$taskKey]); // 移除臨時標記
            $visited[$taskKey] = true; // 標記為已訪問
            $sorted[] = $taskKey; // 添加到已排序的數組中
        };
    }


    // 創建所有任務
    private static function createTasks($methods, $flow, $sortedKeys, $loop)
    {
        $tasks = []; // 初始化任務數組
        $resolvedTasks = []; // 初始化已解決的任務數組

        // 遍歷排序後的任務鍵
        foreach ($sortedKeys as $taskKey) {
            // 獲取該任務的前置任務
            $dependentTasks = isset($flow[$taskKey]) ? $flow[$taskKey] : [];
            // 創建並儲存該任務
            $tasks[$taskKey] = self::createTask($methods, $taskKey, $dependentTasks, $resolvedTasks, $loop);
        };

        // 返回創建的所有任務
        return $tasks;
    }

    // 創建單個任務
    private static function createTask($methods, $taskKey, $tasks, &$resolvedTasks, $loop)
    {
        // 如果該任務已經被解決，直接返回已解決的任務
        if (isset($resolvedTasks[$taskKey])) {
            return $resolvedTasks[$taskKey];
        };

        // 創建一個新的 Promise 延遲對象
        $deferred = new Promise\Deferred();

        // 解決前置任務並創建相應的任務 Promise
        $taskPromises = [];
        foreach ($tasks as $task) {
            // 如果前置任務尚未解決，創建前置任務
            if (!isset($resolvedTasks[$task])) {
                $resolvedTasks[$task] = self::createTask($methods, $task, [], $resolvedTasks, $loop);
            };
            // 將前置任務的 Promise 添加到數組中
            $taskPromises[] = $resolvedTasks[$task];
        };

        // 當所有前置任務解決後，執行當前任務
        Promise\all($taskPromises)->then(function () use ($methods, $taskKey, $deferred) {
            try {
                // 調用當前任務的方法並返回結果
                $result = call_user_func($methods[$taskKey]);
                // 解決當前任務的 Promise
                $deferred->resolve($result);
            } catch (\Exception $e) {
                // 捕獲異常並拒絕當前任務的 Promise
                $deferred->reject($e);
            }
        })->catch(function ($error) use ($deferred) {
            // 捕獲異常並拒絕當前任務的 Promise
            $deferred->reject($error);
        });

        // 將當前任務的 Promise 存儲在已解決的任務數組中
        $resolvedTasks[$taskKey] = $deferred->promise();

        // 返回當前任務的 Promise
        return $resolvedTasks[$taskKey];
    }
}
