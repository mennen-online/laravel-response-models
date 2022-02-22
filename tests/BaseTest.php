<?php

namespace MennenOnline\LaravelResponseModels\Tests;

use MennenOnline\LaravelResponseModels\MennenOnlineHttpResponseProcessorServiceProvider;
use Orchestra\Testbench\TestCase;

class BaseTest extends TestCase
{
    protected function getPackageProviders($app) {
        return [
            MennenOnlineHttpResponseProcessorServiceProvider::class
        ];
    }
}