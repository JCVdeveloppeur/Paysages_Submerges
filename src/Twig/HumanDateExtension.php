<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HumanDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('human_date', [$this, 'getHumanDate']),
        ];
    }

    public function getHumanDate(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->days === 0) {
            return 'aujourd’hui';
        }

        if ($diff->days === 1) {
            return $date > $now ? 'demain' : 'hier';
        }

        $suffix = $date > $now ? 'dans ' : 'il y a ';

        if ($diff->y > 0) {
            return $suffix . $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        }

        if ($diff->m > 0) {
            return $suffix . $diff->m . ' mois';
        }

        if ($diff->d > 0) {
            return $suffix . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        }

        if ($diff->h > 0) {
            return $suffix . $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        }

        return $suffix . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }
}
