<?php

namespace App\Enums\Actions;

use ValueError;

enum SaveAndAction
{
    case TO_MODEL;
    case TO_OVERVIEW;
    case CREATE_AGAIN;

    /**
     * @throw
     */
    public static function fromName(string $name): SaveAndAction
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name){
                return $status;
            }
        }
        throw new ValueError("$name is not a valid backing value for enum " . self::class );
    }


    public static function tryFromName(string $name): SaveAndAction|null
    {
        try {
            return self::fromName($name);
        } catch (ValueError $error) {
            return null;
        }
    }
}
