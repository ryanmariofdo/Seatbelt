<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Contracts;

interface Configuration
{
    /**
     * Whether this configuration should run given the current environment and config.
     */
    public function enabled(): bool;

    /**
     * Apply the configuration.
     */
    public function apply(): void;
}
