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

         // Create an invoice instance
         $invoice = Invoice::make()
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
                    ->pricePerUnit($items->service->price);
                
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

         // Create an invoice instance
         $invoice = Invoice::make()
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
                    ->pricePerUnit($items->service->price);
                
                $invoice->addItem($invoiceItem);
            }
 
         // Generate the PDF invoice
         $pdf = $invoice->stream();
         // You can then return the PDF for download or display
         return response($pdf)->header('Content-Type', 'application/pdf');
     }

     public function generateSaleAcknowledgementReceipt($saleId)
    {
         // Retrieve the record based on the provided record ID
         $record = SaleTransaction::findOrFail($saleId);

         if ($record->customer_id) {
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
         $item = $record->item;

         //$mop = $record->mode_of_payment;

         // Create an invoice instance
         $invoice = Invoice::make()
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
                    ->pricePerUnit($items->service->price);
                
                $invoice->addItem($invoiceItem);
            }
 
         // Generate the PDF invoice
         $pdf = $invoice->stream();
 
         // You can then return the PDF for download or display
         return response($pdf)->header('Content-Type', 'application/pdf');
     }

     public function displayReport($fromDate, $toDate){
        $fromDate = Carbon::parse($fromDate)->format('F j, Y');
        $toDate = Carbon::parse($toDate)->format('F j, Y');
        $sortBy = 'created_at';
    
        $title = 'Sales report';
    
        $meta = [ // For displaying filters description on header
            'Sales on' => $fromDate . ' To ' . $toDate,
            'Sort By' => $sortBy
        ];
    
        $queryBuilder = SaleItem::with('service')
            ->select('service_id', DB::raw('SUM(total_price) as total_price'), DB::raw('SUM(quantity) as quantity'))
            ->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ])
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
            'Total price' => 'total_price'
        ];
    
        return PdfReport::of($title, $meta, $queryBuilder, $columns)
            ->editColumn('Total price', [
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
                'Total price' => '₱ '
            ])
            ->stream();
            }

}
