<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\SaleTransaction;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;

class SalesPerDayChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'salesPerDayChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Sales Per Day Chart';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

     protected function getFormSchema(): array
    {
        return [
            Select::make('type')
            ->options([
                'Walk-in' => 'Walk-in Sales',
                'Online Order' => 'Online Order Sales',
            ]),
            DatePicker::make('date_start')
                ->default(now()->subMonth()),
            DatePicker::make('date_end')
                ->default(now()),
        ];
    }

    protected function getOptions(): array
    {

        $data = Trend::query(
            SaleTransaction::query()
            ->when($this->filterFormData['type'] !== null, function ($query) {
                return $query->where('process_type', $this->filterFormData['type']);
            })
            )
            ->between(
                  start: Carbon::parse($this->filterFormData['date_start'])->startOfDay(), 
                  end: Carbon::parse($this->filterFormData['date_end'])->endOfDay(),
            )
            ->perDay()
            ->sum('total_amount');

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'SalesPerDayChart',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => $value->date),
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
            'stroke' => [
                'curve' => 'smooth',
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
                        // Convert the val (which is in the format "2024-03-07") to a Date object
                        var date = new Date(val);
                        
                        // Get today's date
                        var today = new Date();
                        
                        // Get the month name
                        var monthNames = ["January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"];
                        var month = monthNames[date.getMonth()];

                        // Get the day of the month
                        var day = date.getDate();

                        // Get the year
                        var year = date.getFullYear();

                        // Check if the date is today
                        if (date.toDateString() === today.toDateString()) {
                            return 'Today';
                        }

                        // Format the date as "Month Day, Year" (e.g., "March 7, 2024")
                        return month + ' ' + day + ', ' + year;
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return '₱' + Number(val).toLocaleString();
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
                    return ''
                },
                dropShadow: {
                    enabled: true
                },
            }
        }
        JS);
    }
}
