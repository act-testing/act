<?php

namespace ActTesting\Act\Assert;

use Throwable;
use ActTesting\Act\Runtime\Context;
use ActTesting\Act\Assert\AssertException;

class Assert
{
    public static function true($condition, ?string $message = null): void
    {
        if ($condition !== true) {
            throw new AssertException($message ?? 'Expected condition to be true');
        }
    }

    public static function false($condition, ?string $message = null): void
    {
        if ($condition !== false) {
            throw new AssertException($message ?? 'Expected condition to be false');
        }
    }

    public static function equals($expected, $actual, ?string $message = null): void
    {
        if ($expected != $actual) {
            $expectedStr = var_export($expected, true);
            $actualStr = var_export($actual, true);
            throw new AssertException($message ?? "Expected $expectedStr, got $actualStr");
        }
    }

    public static function contains(string $needle, string $haystack, ?string $message = null): void
    {
        if (strpos($haystack, $needle) === false) {
            throw new AssertException($message ?? "Expected to find '$needle' in response");
        }
    }

    public static function fail(string $message): void
    {
        throw new AssertException($message);
    }

    public static function exception(Context $context, ?string $message = null): void
    {
        $exception = $context->exception;

        if ($message !== $exception->getMessage()) {
            throw new AssertException($message ?? 'Expected an exception, got ' . $exception->getMessage());
        }

        $context->remove('exception');
    }
}
