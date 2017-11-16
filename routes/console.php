<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('Order:Assign', function () {
    $this->comment(\App\Console\Commands\OrderAssign::class);
})->describe('Order assign');

Artisan::command('Order:AssignTemp', function () {
    $this->comment(\App\Console\Commands\OrderAssignTemp::class);
})->describe('Order assign');

Artisan::command('Order:TestData', function () {
    $this->comment(\App\Console\Commands\OrderTestData::class);
})->describe('Order TestData');