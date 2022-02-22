<?php

namespace MennenOnline\LaravelHttpResponseProcessor\Tests;

use MennenOnline\LaravelHttpResponseProcessor\MennenOnlineHttpResponseProcessorServiceProvider;
use Orchestra\Testbench\TestCase;

class BaseTest extends TestCase
{
    protected function getPackageProviders($app) {
        return [
            MennenOnlineHttpResponseProcessorServiceProvider::class
        ];
    }
}