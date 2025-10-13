<?php

namespace Tests\TestCase;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\TestCase\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
