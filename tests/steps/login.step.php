<?php

use ActTesting\Act\Assert\Assert;
use ActTesting\Act\Runtime\Context;
use function ActTesting\Act\Steps\then;
use function ActTesting\Act\Steps\when;
use function ActTesting\Act\Steps\given;
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

then('they are redirected to the home page', function (Context $test) {
    //
});

when('they enter invalid credentials', function (Context $test) {
});

then('they are not authenticated', function (Context $test) {
});