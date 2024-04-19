<?php

declare(strict_types=1);

namespace App\OutputModel;

class UnifyOutputModel
{
    public function __construct(
        public int $id,
        public string $name,
        public int $totalSteps
    ) {
    }
}