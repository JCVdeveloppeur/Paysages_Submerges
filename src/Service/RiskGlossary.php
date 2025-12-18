<?php

namespace App\Service;

final class RiskGlossary
{
    /**
     * Retourne les infos “Niveau de risque” prêtes à afficher.
     * $level attendu: faible | moyenne | elevee | null
     */
    public function get(?string $level): array
    {
        $level = $level ?: '';

        $default = [
            'level' => '',
            'label' => 'À évaluer',
            'tooltip' => "Niveau de risque non renseigné.\nBase-toi sur les symptômes et la vitesse d’évolution.",
            'tooltipClass' => 'risque-tooltip',
        ];

        return match ($level) {
            'faible' => [
                'level' => 'faible',
                'label' => '🟢 Risque limité',
                'tooltip' => "Généralement bénin si pris tôt.\nÀ surveiller : appétit, nage, respiration.\nMesures : isolement + contrôle eau.",
                'tooltipClass' => 'risque-tooltip risque-tooltip--faible',
            ],
            'moyenne' => [
                'level' => 'moyenne',
                'label' => '🟠 Risque modéré',
                'tooltip' => "Peut s'aggraver sans prise en charge.\nRecommandé : bac hôpital + traitement adapté.\nSurveille les signes 24–48h.",
                'tooltipClass' => 'risque-tooltip risque-tooltip--moyenne',
            ],
            'elevee' => [
                'level' => 'elevee',
                'label' => '🔴 Risque important',
                'tooltip' => "Urgence relative : forte contagion/gravité possible.\nAgir vite : isolement immédiat + traitement.\nVérifie NH3/NO2 et oxygénation.",
                'tooltipClass' => 'risque-tooltip risque-tooltip--elevee',
            ],
            default => $default,
        };
    }
}
