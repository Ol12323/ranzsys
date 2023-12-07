<?php

namespace App\Filament\Widgets;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\SaleTransaction; // Replace with your actual namespace
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\HtmlString;

class SaleTransactionsChart extends ChartWidget
{
    protected static ?string $heading = 'Process distribution chart ₱';

    protected static ?string $pollingInterval = '10s';
    
    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '275px';
    
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                scales: {
                    y: {
                    display: false, // Hide the y-axis ticks
                },
                x: {
                    display: false, // Hide the y-axis ticks
                },
                },
            }
        JS);
    }

    protected function getData(): array
    {
       
        $data = SaleTransaction::select('process_type', DB::raw('SUM(total_amount) as total_amount_sum'))
        ->groupBy('process_type')
        ->get();
    
        $labels = $data->pluck('process_type');
        $totals = $data->pluck('total_amount_sum');

        return [
            'datasets' => [
                [
                    'data' => $totals,
                    'backgroundColor' => [
                        '#2563EB',
                        '#00A859'
                      ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
