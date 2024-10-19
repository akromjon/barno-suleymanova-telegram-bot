<?php

namespace App\Filament\Widgets;

use App\Models\TelegramUser;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TelegramUserTrendWidget extends ChartWidget
{
    protected static ?string $pollingInterval = '5s';
    protected static ?string $heading = 'New Users';
    protected static string $color = 'success';
    public ?string $filter = 'week';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = "300px";

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
            'all' => 'All time',
        ];
    }

    protected function getData(): array
    {
        $filterData = $this->getFilterData();

        $data = $this->fetchData(filterData: $filterData);

        return [
            'datasets' => [
                [
                    'label' => 'Users Registered',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format($this->filter === 'today' ? 'H:i' : 'd.m.Y')),
        ];
    }

    protected function getFilterData(): array
    {
        return [
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
                'per' => 'perHour'
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
                'per' => 'perDay'
            ],
            'month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
                'per' => 'perDay'
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
                'per' => 'perMonth'
            ],
            'all' => [
                'start' => now()->subYears(1),
                'end' => now(),
                'per' => 'perMonth'
            ],
        ];
    }

    protected function fetchData(array $filterData)
    {
        return Trend::model(TelegramUser::class)
                    ->between(
                        start: $filterData[$this->filter]['start'],
                        end: $filterData[$this->filter]['end'],
                    )
            ->{$filterData[$this->filter]['per']}()
                ->count();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
