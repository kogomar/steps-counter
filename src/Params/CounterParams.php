<?php

declare(strict_types=1);

namespace App\Params;

use Symfony\Component\Validator\Constraints as Assert;

class CounterParams
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    #[Assert\Length(min: 1)]
    #[Assert\Positive]
    public int $stepCount = 0;
}