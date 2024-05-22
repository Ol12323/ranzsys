<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use Filament\Router\FilamentRouter;
use App\Livewire\CatalogServices;
use App\Livewire\ShowService;
use App\Http\Controllers\CustomerServiceController;
use App\Filament\Pages\ViewService;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CustomerServiceController::class, 'index'])->name('home')->middleware('prevent.authenticated');

Route::get('/catalog', [CustomerServiceController::class, 'catalog'])->name('catalog');
Route::get('/service/{id}', [CustomerServiceController::class, 'view'])->name('view-service');
Route::get('/search/', [CustomerServiceController::class, 'search'])->name('search');

Route::get('/generate-invoice/order/{record}', [InvoiceController::class, 'generateOrderInvoice'])
    ->name('generate.order-invoice');

Route::get('/generate-acknowledgement-receipt/order/{record}', [InvoiceController::class, 'generateAcknowledgementReceipt'])
    ->name('generate.order-acknowledgement-receipt');

Route::get('/generate-acknowledgement-receipt/sale/{record}', [InvoiceController::class, 'generateSaleAcknowledgementReceipt'])
    ->name('generate.sale-acknowledgement-receipt');
    
Route::get('/generate-sales-per-service-report/from/{fromDate}/to/{toDate}', [InvoiceController::class, 'salesPerService'])
    ->name('generate.sales-per-service-report');

Route::get('/generate-sales-per-transaction-report/from/{fromDate}/to/{toDate}', [InvoiceController::class, 'salesPerTransaction'])
    ->name('generate.sales-per-transaction-report');

Route::get('/add-to-cart/{id}', [CustomerServiceController::class, 'addToCart'])->name('add-to-cart');
Route::post('/set-appointment', [CustomerServiceController::class, 'setAppointment'])->name('setAppointment');

Route::get('/my-order', [CustomerServiceController::class, 'myOrder'])->name('myOrder');

// Route::get('customer/service/{id}', ViewService::class)->name('test');