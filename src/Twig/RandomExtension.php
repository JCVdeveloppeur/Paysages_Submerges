<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RandomExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('random_element', [$this, 'getRandomElement']),
        ];
    }

    public function getRandomElement(array $array)
    {
        return $array[array_rand($array)];
    }
}
