<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Models\Appointment;
use App\Models\SaleTransaction;
use Illuminate\Support\Carbon;
use App\Models\SaleItem;
use App\Models\User;
use App\Models\Order;
use LaravelDaily\Invoices\Classes\Party;
use PdfReport;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Jimmyjs\ReportGenerator\ReportMedia\PdfReport as ReportMediaPdfReport;

class InvoiceController extends Controller
{
    
     public function generateOrderInvoice($record)
    {
         // Retrieve the record based on the provided record ID
         $record = Order::findOrFail($record);

         if ($record->user_id) {
            $customer = User::findOrFail($record->user_id);
            $buyer = new Buyer([
                'name' => $customer->full_name,
                'custom_fields' => [
                    'email' => $customer->email,
                    // Add other custom fields as needed
                ],
            ]);
        } else {
            // Handle guest scenario (formerly walk-in)
            $buyer = new Buyer([
                'name' => 'Guest Customer',
                // Add other custom fields for guest scenario
            ]);
        }

         $seller = new Party([
            'name' => 'Ranz Photography',
            'address' => 'Door 1 Grageda Bldg. Quezon St. New Pandan',
            'custom_fields' => [
                'email' => 'photography.ranz@gmail.com',
                // Add other custom fields as needed
            ],
        ]);
         // Create an item instance
         $item = $record->service;

         $mop = $record->mode_of_payment;

         $dueDate = $record->service_date;

         $invoiceNumber = $record->order_name;

         // Create an invoice instance
         $invoice = Invoice::make()
             ->serialNumberFormat($invoiceNumber)
             ->buyer($buyer)
             ->currencySymbol('₱')
             ->currencyCode('peso')
             ->notes('Not an official receipt.');
             if ($mop === 'g-cash-partial') {
                $invoice->discountByPercent(50)
                        ->seller($seller)
                        ->logo('images/ranz-logo.jpg');
            } elseif ($mop === 'g-cash') {
                $invoice->discountByPercent(100)
                        ->seller($seller)
                        ->logo('images/ranz-logo.jpg');
            }else 
            {
                $invoice->seller($seller)
                        ->logo('images/ranz-logo.jpg');
            }

             foreach ($item as $items) {
                $invoiceItem = (new InvoiceItem())
                    ->title($items->service->service_name)
                    ->quantity($items->quantity)
                    ->pricePerUnit($items->service->price)
                    ->subTotalPrice($items->subtotal);
                
                $invoice->addItem($invoiceItem);
            }
 
         // Generate the PDF invoice
         $pdf = $invoice->stream();
 
         // You can then return the PDF for download or display
         return response($pdf)->header('Content-Type', 'application/pdf');
     }

     public function generateAcknowledgementReceipt($record)
    {
         // Retrieve the record based on the provided record ID
         $record = Order::findOrFail($record);

         if ($record->user_id) {
            $customer = User::findOrFail($record->user_id);
            $buyer = new Buyer([
                'name' => $customer->full_name,
                'custom_fields' => [
                    'email' => $customer->email,
                    // Add other custom fields as needed
                ],
            ]);
        } else {
            // Handle guest scenario (formerly walk-in)
            $buyer = new Buyer([
                'name' => 'Guest Customer',
                // Add other custom fields for guest scenario
            ]);
        }

         $seller = new Party([
            'name' => 'Ranz Photography',
            'address' => 'Door 1 Grageda Bldg. Quezon St. New Pandan',
            'custom_fields' => [
                'email' => 'photography.ranz@gmail.com',
                // Add other custom fields as needed
            ],
        ]);
         // Create an item instance
         $item = $record->service;

         $mop = $record->mode_of_payment;

         $invoiceNumber = $record->order_name;

         // Create an invoice instance
         $invoice = Invoice::make()
             ->serialNumberFormat($invoiceNumber)
             ->buyer($buyer)
             ->name('Acknowledgement Receipt')
             ->currencySymbol('₱')
             ->currencyCode('peso')
             ->notes('Not an official receipt.')
             ->seller($seller)
             ->status(__('invoices::invoice.paid'))
             ->template('acknowledgement-receipt')
             ->logo('images/ranz-logo.jpg');
            

             foreach ($item as $items) {
                $invoiceItem = (new InvoiceItem())
                    ->title($items->service->service_name)
                    ->quantity($items->quantity)
                    ->pricePerUnit($items->service->price)
                    ->subTotalPrice($items->subtotal);
                $invoice->addItem($invoiceItem);
            }
 
         // Generate the PDF invoice
         $pdf = $invoice->stream();
         // You can then return the PDF for download or display
         return response($pdf)->header('Content-Type', 'application/pdf');
     }

     public function generateSaleAcknowledgementReceipt($record)
    {
         // Retrieve the record based on the provided record ID
         $record = SaleTransaction::findOrFail($record);

         if ($record->customer_id) {
            $customer = User::findOrFail($record->customer_id);
            $buyer = new Buyer([
                'name' => $customer->full_name,
                'custom_fields' => [
                    'email' => $customer->email,
                    // Add other custom fields as needed
                ],
            ]);
        } else {
            // Handle guest scenario (formerly walk-in)
            $buyer = new Buyer([
                'name' => 'Guest Customer',
                // Add other custom fields for guest scenario
            ]);
        }

         $seller = new Party([
            'name' => 'Ranz Photography',
            'address' => 'Door 1 Grageda Bldg. Quezon St. New Pandan',
            'custom_fields' => [
                'email' => 'photography.ranz@gmail.com',
                // Add other custom fields as needed
            ],
        ]);
         // Create an item instance
         $item = $record->item;

         $invoiceNumber = $record->sales_name;
         // Create an invoice instance
         $invoice = Invoice::make()
             ->serialNumberFormat($invoiceNumber)
             ->buyer($buyer)
             ->name('Acknowledgement Receipt')
             ->currencySymbol('₱')
             ->currencyCode('peso')
             ->notes('Not an official receipt.')
             ->seller($seller)
             ->status(__('invoices::invoice.paid'))
             ->template('acknowledgement-receipt')
             ->logo('images/ranz-logo.jpg');
            

             foreach ($item as $items) {
                $invoiceItem = (new InvoiceItem())
                    ->title($items->service->service_name)
                    ->quantity($items->quantity)
                    ->pricePerUnit($items->service->price)
                    ->subTotalPrice($items->total_price);
                
                $invoice->addItem($invoiceItem);
            }
 
         // Generate the PDF invoice
         $pdf = $invoice->stream();
 
         // You can then return the PDF for download or display
         return response($pdf)->header('Content-Type', 'application/pdf');
     }

     public function salesPerService($fromDate, $toDate){

      try{
        // Parse the dates and keep them in Carbon format for the query
        $fromDateStart = Carbon::parse($fromDate);
        $fromDateParsed = Carbon::parse($fromDate)->subDay();
        $toDateParsed = Carbon::parse($toDate)->endOfDay();

        // Format the dates for display
        $fromDateDisplay = $fromDateStart->format('F j, Y');
        $toDateDisplay = $toDateParsed->format('F j, Y');

        $authFullName = Auth::user()->full_name;

        $sortBy = 'created_at';

        $title = 'Sales Per Service Report';

        $meta = [ // For displaying filters description on header
            'Sales on' => $fromDateDisplay . ' To ' . $toDateDisplay,
            'Sort By' => 'Total Price',
            'Generated By' => $authFullName,
            'Generated At' => now()->format('F j, Y'),
        ];

        $queryBuilder = SaleItem::with('service')
            ->select('service_id', DB::raw('SUM(total_price) as total_price'), DB::raw('SUM(quantity) as quantity'))
            ->whereBetween('created_at', [$fromDateParsed, $toDateParsed])
            ->orderBy('total_price', 'desc')
            ->groupBy('service_id');
    
        $columns = [
            'Service Name' => function($result) {
                return $result->service->service_name;
            },
            'Price' => function($result) {
                return 'PHP ' . $result->service->price;
            },
            'Qty' => 'quantity',
            'Total Price' => 'total_price'
        ];
    
        return PdfReport::of($title, $meta, $queryBuilder, $columns)
            ->editColumn('Total Price', [
                'displayAs' => function ($result) {
                    return '₱ ' . number_format($result->total_price, 2);
                },
                'class' => 'left'
            ])
            ->editColumn('Price', [
                'displayAs' => function ($result) {
                    return '₱ ' . number_format($result->service->price, 2);
                },
                'class' => 'left'
            ])
            ->showTotal([
                'Total Price' => '₱ '
            ])
            ->stream();
                } catch (Exception $e) {
                    // Handle exception and send a notification
                    Notification::make()
                        ->title('Failed to generate report')
                        ->body('An error occurred while generating the report. Please try again later.')
                        ->color('danger')
                        ->send();
            
                    return redirect()->back()->with('error', 'Failed to generate report. Please try again later.');
                }
            }

        public function salesPerTransaction($fromDate, $toDate){
            try{
                 // Parse the dates and keep them in Carbon format for the query
                $fromDateStart = Carbon::parse($fromDate);
                $fromDateParsed = Carbon::parse($fromDate)->subDay();
                $toDateParsed = Carbon::parse($toDate)->endOfDay();

                // Format the dates for display
                $fromDateDisplay = $fromDateStart->format('F j, Y');
                $toDateDisplay = $toDateParsed->format('F j, Y');

                $authFullName = Auth::user()->full_name;

                $sortBy = 'created_at';

                $title = 'Sales Per Transaction Report';

                $meta = [ // For displaying filters description on header
                    'Sales on' => $fromDateDisplay . ' To ' . $toDateDisplay,
                    'Sort By' => 'Transaction Date',
                    'Generated by' => $authFullName,
                    'Generated At' => now()->format('F j, Y'),
                ];

                
                $queryBuilder = SaleTransaction::with(['customer', 'staff'])
                    ->select('sales_name','process_type', 'customer_id', 'processed_by', 'total_amount', 'created_at')
                    ->whereBetween('created_at', [$fromDateParsed, $toDateParsed])
                    ->orderBy('created_at');
        
                $columns = [
                    'Invoice No.' => 'sales_name',
                    'Process Type' => 'process_type',
                    'Customer' => function($result) {
                        return $result->customer_id === null ? 'Guest Customer' : $result->customer->full_name;
                    },
                    'Processed By' => function($result) {
                        return $result->staff->full_name;
                    },
                    'Transaction Date' => 'created_at',
                    'Total Amount' => 'total_amount'
                ];
            
                return PdfReport::of($title, $meta, $queryBuilder, $columns)
                    ->setOrientation('portrait')
                    ->editColumn('Total Amount', [
                        'displayAs' => function ($result) {
                            return '₱ ' . number_format($result->total_amount, 2);
                        },
                        'class' => 'left'
                    ])
                    ->editColumn('Transaction Date', [
                        'displayAs' => function ($result) {
                            $transactionDate = Carbon::parse($result->created_at);

                            return $formatTransactionDate = $transactionDate->format('F j, Y');
                        },
                        'class' => 'left'
                    ])
                    ->showTotal([
                        'Total Amount' => '₱ '
                    ])
                    ->stream();
                    } catch (Exception $e) {
                        // Handle exception and send a notification
                        Notification::make()
                            ->title('Failed to generate report')
                            ->body('An error occurred while generating the report. Please try again later.')
                            ->color('danger')
                            ->send();
                
                        return redirect()->back()->with('error', 'Failed to generate report. Please try again later.');
                    }
                 } 
}
