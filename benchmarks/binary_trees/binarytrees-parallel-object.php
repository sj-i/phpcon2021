<?php

/* The Computer Language Benchmarks Game
   https://salsa.debian.org/benchmarksgame-team/benchmarksgame/

   contributed by Léo Sarrazin
   multi thread by Andrey Filatkin
   translated to PHP by sj-i
*/
// The original version can be found here
// https://salsa.debian.org/benchmarksgame-team/benchmarksgame/-/blob/a8ee6270f9274acb959526584b588fe11075a52c/public/program/binarytrees-node-6.html

use parallel\Channel;

function isMainThread(): bool
{
    try {
        Channel::open('main_flag');
        return false;
    } catch (\Throwable) {
        Channel::make('main_flag');
        return true;
    }
}

gc_disable();
if (isMainThread()) {
    mainThread($argv);
}

function logger(string $message): void
{
    echo $message, PHP_EOL;
}

function mainThread(array $argv): void
{
    $maxDepth = max(6, $argv[1]);

    $stretchDepth = $maxDepth + 1;
    $check = itemCheck(bottomUpTree($stretchDepth));
    logger("stretch tree of depth {$stretchDepth}\t check: {$check}");

    $longLivedTree = bottomUpTree($maxDepth);

    $tasks = [];
    for ($depth = 4; $depth <= $maxDepth; $depth += 2) {
        $iterations = 1 << $maxDepth - $depth + 4;
        $tasks[] = [$iterations, $depth];
    }

    $results = runTasks($tasks);
    foreach ($results as $result) {
        logger($result);
    }

    logger("long lived tree of depth {$maxDepth}\t check: " . itemCheck($longLivedTree));
}

function workerThread(int $iterations, int $depth): string
{
    return work($iterations, $depth);
}

function runTasks(array $tasks): array
{
    $results = [];
    $tasksSize = count($tasks);

    $workers = [];
    for ($i = 0; $i < count($tasks); $i++) {
        $worker = new parallel\Runtime(__FILE__);
        $future = $worker->run(\Closure::fromCallable('workerThread'), $tasks[$i]);
        $workers[] = [$worker, $future, $i];
    }
    foreach ($workers as [$worker, $future, $i]) {
        $results[$i] = $future->value();
        $tasksSize--;
        if ($tasksSize === 0) {
            break;
        }
    }
    return $results;
}

function work(int $iterations, int $depth): string
{
    $check = 0;
    for ($i = 0; $i < $iterations; $i++) {
        $check += itemCheck(bottomUpTree($depth));
    }
    return "{$iterations}\t trees of depth {$depth}\t check: {$check}";
}

class TreeNode
{
    public function __construct(public $left, public $right)
    {
    }
}

function bottomUpTree($depth)
{
    return $depth > 0
        ? new TreeNode(bottomUpTree($depth - 1), bottomUpTree($depth - 1))
        : new TreeNode(null, null);
}

function itemCheck($node)
{
    if ($node->left === null) {
        return 1;
    }
    return 1 + itemCheck($node->left) + itemCheck($node->right);
}
