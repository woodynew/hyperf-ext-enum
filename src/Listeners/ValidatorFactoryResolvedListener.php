<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Listeners;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use HyperfExt\Enum\Rules\Enum;
use HyperfExt\Enum\Rules\EnumKey;
use HyperfExt\Enum\Rules\EnumValue;

class ValidatorFactoryResolvedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var \Hyperf\Validation\Contract\ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;

        $validatorFactory->extend('enum_key', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;
            return (new EnumKey($enum))->passes($attribute, $value);
        });

        $validatorFactory->extend('enum_value', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;
            $strict = $parameters[1] ?? null;
            if (! $strict) {
                return (new EnumValue($enum))->passes($attribute, $value);
            }
            $strict = (bool) json_decode(strtolower($strict));
            return (new EnumValue($enum, $strict))->passes($attribute, $value);
        });

        $validatorFactory->extend('enum', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;
            return (new Enum($enum))->passes($attribute, $value);
        });
    }
}
