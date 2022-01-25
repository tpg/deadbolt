<?php

declare(strict_types=1);

namespace TPG\Deadbolt;

use JetBrains\PhpStorm\Pure;

class Permission
{
    public function __construct(...$args)
    {
        foreach ($args as $key => $value) {

            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }

        }
    }

    public static function create(string $name, string|null $group = null, string|null $description = null): self
    {
        return new self(
            name: $name,
            group: $group,
            description: $description,
        );
    }
}
