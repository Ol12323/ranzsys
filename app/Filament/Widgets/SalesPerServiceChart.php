<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

class SalesPerServiceChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'salesPerServiceChart';

    protected static ?int $sort = 2;

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Top 10 Best-Selling Services by Total Revenue';

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
        $data = SaleItem::with('service')
        ->select('service_id', DB::raw('SUM(total_price) as total_price'))
        ->whereRaw("created_at BETWEEN ? AND ?", [
            Carbon::parse($this->filterFormData['date_start'])->startOfDay(),
            Carbon::parse($this->filterFormData['date_end'])->endOfDay(),
        ])
        ->groupBy('service_id')
        ->orderBy('total_price', 'desc') // Order by total_price in descending order
        ->limit(10) // Limit the results to 10
        ->get();

        $labels = $data->map(function ($item) {
            return $item->service->service_name;
        });
        
        $salesTotals = $data->pluck('total_price');
      
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Sale ₱',
                    'data' => $salesTotals,
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
                    return '₱' + Number(val).toLocaleString();
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
                return '₱' + Number(val).toLocaleString();
            },
            dropShadow: {
                enabled: true
            },
        }
    }
    JS);
}
}
