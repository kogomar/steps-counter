<?php

declare(strict_types=1);

namespace App\Params;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCounterParams extends CounterParams
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    public string $name;
}