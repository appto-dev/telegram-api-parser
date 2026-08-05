<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\RevenueWithdrawalStateFailed;
use Appto\TelegramBot\Type\RevenueWithdrawalStatePending;
use Appto\TelegramBot\Type\RevenueWithdrawalStateSucceeded;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class RevenueWithdrawalStateCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'pending' => RevenueWithdrawalStatePending::from($value),
            'succeeded' => RevenueWithdrawalStateSucceeded::from($value),
            'failed' => RevenueWithdrawalStateFailed::from($value),
            default => Uncastable::create(),
        };
    }
}
