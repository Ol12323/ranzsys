<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales per service chart ₱';

    protected static ?string $pollingInterval = '10s';
    
    protected static ?int $sort = 2;

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => '₱ ' + value,
                        },
                    },
                },
            }
        JS);
    }

    protected function getData(): array
    {
        $data = SaleItem::with('service')
        ->select('service_id', DB::raw('SUM(total_price) as total_price'))
        ->groupBy('service_id')
        ->get();

        $labels = $data->map(function ($item) {
            return $item->service->service_name;
        });
        $salesTotals = $data->pluck('total_price');

        return [
                'datasets' => [
                    [
                        'label' => 'Total sales',
                        'data' => $salesTotals,
                        'backgroundColor' => [
                            '#2563EB',
                          ],
                          'borderColor' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'labels' => $labels,
        
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
