<?php

namespace App\Filament\Customer\Resources\AppointmentResource\Pages;

use App\Filament\Customer\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Redirect;
use App\Models\Appointment;
use App\Models\Message;
use Filament\Forms\Components\TextArea;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\DisabledDate;
use App\Models\TimeSlot;
use Filament\Forms\Get;
use Closure;
use App\Models\User;
use Filament\Notifications\Actions\Action as NotifAction;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendMessage')
            ->outlined()
            ->color('success')
            ->form([
                Textarea::make('content')
                ->required(),
            ])
            ->action(function (array $data): void {
                $message = new Message([
                    'sender_id' => auth()->user()->id,
                    'recipient_id' => 1,
                    'subject' => 'Online appointment:'.' '.$this->record->name,
                    'content' => $data['content'],
                    'read' => false,
                ]);
                $message->save();
    
                Notification::make()
                ->title('Message sent successfully.')
                ->success()
                ->send();
    
            }),
            Action::make('viewPaymentMethodDetails')
            ->modalSubmitAction(false)
            ->outlined()
            ->color('gray')
            ->form([
                Placeholder::make('modeOfPayment')
                ->content(function (Model $record) {
                    $mop = $record->mode_of_payment;
                    if($mop === 'cash'){
                        return new HtmlString(Blade::render(<<<BLADE
                        <x-filament::badge color="success"
                        >
                        $mop
                        </x-filament::badge>
                        BLADE)); 
                    }elseif($mop === 'g-cash'){
                        return new HtmlString(Blade::render(<<<BLADE
                        <x-filament::badge color="primary"
                        >
                        $mop
                        </x-filament::badge>
                        BLADE)); 
                    }else{
                        return new HtmlString(Blade::render(<<<BLADE
                        <x-filament::badge color="info"
                        >
                        $mop
                        </x-filament::badge>
                        BLADE)); 
                    }
                 }),
                Placeholder::make('totalAmount')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues;
 
                    return '₱'.$total; 
                 }),
                 Placeholder::make('note')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues;
 
                    return 'Please be advised that the selected mode of payment for your order is cash on appointment date. We kindly request that you prepare the total amount due on the appointment date. Thank you for your cooperation, and we look forward to serving you.'; 
                 })
                ->visible(function (Model $record) {
                    return $record->mode_of_payment === 'cash';
                }
               ),
                FileUpload::make('fileImage')
                ->label('Payment screenshot')
                ->downloadable()
                ->disabled()
                ->openable()
                ->default(function (Model $record) {
                   $file = $record->receipt_screenshot;

                   return $file;
                    
                })
                ->visible(
                    function (Model $record) {
                        return $record->mode_of_payment === 'g-cash';
                    }
                ),
                FileUpload::make('fileImage')
                ->label('Partial payment screenshot')
                ->downloadable()
                ->disabled()
                ->openable()
                ->default(function (Model $record) {
                   $file = $record->receipt_screenshot;

                   return $file;
                    
                })
                ->visible(
                    function (Model $record) {
                        return $record->mode_of_payment === 'g-cash-partial';
                    }
                ),
            ]),
            Actions\Action::make('rescheduleAppointment')
            ->color('info')
            ->visible(function (Model $record){
                return $record->status === 'Missed';
            })
            ->form([
                DatePicker::make('appointment_date')
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                        $time = $get('time_slot_id');
                        $date = $value;
        
                        $not_unique = Appointment::where([
                            ['appointment_date', '=', $date],
                            ['time_slot_id', '=', $time],
                        ])->exists();

                        if($not_unique){
                            $fail('The selected appointment date and time slot are already in use.');
                        }
                    },
                ])
                    ->native(false)
                    ->required()
                    ->label('New date')
                    ->closeOnDateSelection()
                    ->minDate(now())
                    ->disabledDates(
                        function() {
                            return DisabledDate::pluck('disabled_date')->toArray();
                        }
                    ),
                Select::make('time_slot_id')
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $date = $get('appointment_date');
                            $time = $value;
            
                            $not_unique = Appointment::where([
                                ['appointment_date', '=', $date],
                                ['time_slot_id', '=', $time],
                            ])->exists();

                            if($not_unique){
                                $fail('The selected appointment date and time slot are already in use.');
                            }
                        },
                    ])
                    ->label('New timeslot')
                    ->options(TimeSlot::all()->pluck('time_slot', 'id'))
                    ->required(),
            ])
            ->action(function (array $data, Model $record){
                       $record->appointment_date = $data['appointment_date'];
                       $record->time_slot_id    = $data['time_slot_id'];
                       $record->status = 'Scheduled';
                       $record->save();

                       Notification::make()
                        ->title('Appointment rescheduled successfully.')
                        ->success()
                        ->send();

                        $recipients = User::whereIn('role_id', [1, 2])->get();
                        $appoinment_id = $record->id;
                        
                        Notification::make()
                            ->title('Appointment rescheduled.')
                            ->success()
                            ->actions([
                                NotifAction::make('view')
                                    ->button()
                                    ->url("/owner/appointments/{$appoinment_id}"),
                            ])
                            ->sendToDatabase($recipients);
                 
            }),
            Actions\Action::make('generateBillingInvoice')
            ->hidden(function (Model $record){
                return abs($record->payment_due) < 0.01;
            })
            ->url(fn (Appointment $record): string => route('generate.invoice', $record))
            ->openUrlInNewTab(),
        ];
    }
}
