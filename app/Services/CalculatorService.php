<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class CalculatorService
{
    protected float $defaultTarget;
    protected float $inflationRate;

    public function __construct()
    {
        $this->defaultTarget = Config::get('kopi.default_target', 100000);
        $this->inflationRate = Config::get('kopi.inflation_rate', 0.09);
    }

    public function calculate(float $cost, int $days): array
    {
        $yearly = $cost * $days;

        $target = $this->defaultTarget;

        $percent = min(($yearly / $target) * 100, 100);

        $realValue = $yearly / (1 + $this->inflationRate);

        $monthlyData = $this->getMonthlyData($cost, $days);

        return [
            'yearly' => round($yearly, 2),
            'target' => $target,
            'percent' => round($percent, 1),
            'real_value' => round($realValue, 2),
            'inflation_rate' => $this->inflationRate * 100,
            'two_years' => round($yearly * 2, 2),
            'five_years' => round($yearly * 5, 2),
            'ten_years' => round($yearly * 10, 2),
            'monthly' => $monthlyData,
            'frequency_label' => $this->getFrequencyLabel($days),
        ];
    }

    public function getMonthlyData(float $cost, int $days): array
    {
        $monthly = ($cost * $days) / 12;
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = round($monthly * $i, 2);
        }

        return $data;
    }

    public function getFrequencyLabel(int $days): string
    {
        return match ($days) {
            365 => 'Каждый день',
            260 => 'По будням',
            104 => 'По выходным',
            52 => 'Раз в неделю',
            12 => 'Раз в месяц',
            default => $days . ' раз в год',
        };
    }

    public function getFrequencies(): array
    {
        return [
            365 => 'Каждый день',
            260 => 'По будням (5/7)',
            104 => 'По выходным',
            52 => 'Раз в неделю',
            12 => 'Раз в месяц',
        ];
    }
}