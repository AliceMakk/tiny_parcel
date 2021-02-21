<?php
namespace App\Service;

use App\Repository\RateRepository;

class PriceCalculator
{
    const WEIGHT_UNIT_NAME = 'weight';
    const VALUE_UNIT_NAME = 'value';
    const VOLUME_UNIT_NAME = 'volume';

    public function getMaxRate(RateRepository $rateRepository, $weight, $volume, $declaredValue): array
    {
        $result = [
            'maxPrice' => 0,
            'rateEntity' => null,
        ];

        $rates = $rateRepository->findAll();
        foreach ($rates as $rate) {
            $price = 0;
            if ($weight && $rate->getUnit()->getName() === self::WEIGHT_UNIT_NAME) {
                $price = $rate->getRate() * $weight;
            } else if ($volume && $rate->getUnit()->getName() === self::VOLUME_UNIT_NAME) {
                $price = $rate->getRate() * $volume;
            } else if ($declaredValue && $rate->getUnit()->getName() === self::VALUE_UNIT_NAME) {
                $price = ($rate->getRate() * $declaredValue) / 100;
            }
            if ($price > $result['maxPrice']) {
                $result = [
                    'maxPrice' => round($price, 2),
                    'rateEntity' => $rate,
                ];
            }
        }

        return $result;
    }
}