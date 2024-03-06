<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\SaleTransaction; // Replace with your actual namespace
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

class OrderTypeChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'orderTypeChart';

    protected static ?int $sort = 3;

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Sales per order type';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

     protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_start')
                ->default(now()->subMonth()),
            DatePicker::make('date_end')
                ->default(now()),
        ];
    }
    protected function getOptions(): array
    {
        $data = SaleTransaction::select('process_type', DB::raw('SUM(total_amount) as total_amount_sum'))
        ->groupBy('process_type')
        ->whereRaw("created_at BETWEEN ? AND ?", [
            Carbon::parse($this->filterFormData['date_start']),
            Carbon::parse($this->filterFormData['date_end'])->addDay(),
        ])
        ->get();
    
        $labels = $data->pluck('process_type');
        $totals = $data->pluck('total_amount_sum');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Sale ₱',
                    'data' => $totals,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#2563EB'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    formatter: function (val, timestamp, opts) {
                        return '₱' + val
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return val
                    }
                }
            },
            tooltip: {
                x: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opt) {
                    return '₱' + val
                },
                dropShadow: {
                    enabled: true
                },
            }
        }
        JS);
    }
}
