<?php

use ActTesting\Act\Assert;
use ActTesting\Act\Context;
use function ActTesting\Act\then;
use function ActTesting\Act\when;
use function ActTesting\Act\given;
use Tests\Fixtures\AuthService;

given('a user exists', function (Context $test) {
    $test->auth = new AuthService();
    $test->auth->addUser('john@example.com', 'secret');
});

when('they enter valid credentials', function (Context $test) {
    $test->loginResult = $test->auth->attempt('john@example.com', 'secret');
});

then('they are authenticated', function (Context $test) {
    Assert::true($test->loginResult, 'Expected authentication to succeed');
});
