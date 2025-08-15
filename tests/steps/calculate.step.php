<?php

use Tests\Fixtures\Calculator;
use ActTesting\Act\Assert\Assert;
use ActTesting\Act\Runtime\Context;
use function ActTesting\Act\Steps\then;
use function ActTesting\Act\Steps\when;
use function ActTesting\Act\Steps\given;

given('I have a calculator named [name]', function (Context $test, string $name) {
    $test->calculator = new Calculator($name);
});

when('I add [a] and [b]', function (Context $test, int $a, int $b) {
    $test->result = $test->calculator->add($a, $b);
});

then('I get [result]', function (Context $test, int $result) {
    Assert::equals($result, $test->result);
});
