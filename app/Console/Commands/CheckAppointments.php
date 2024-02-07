<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotifAction;
use Carbon\Carbon;

class CheckAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->format('Y-m-d'); // Get tomorrow's date in the Manila/Asia timezone
        
        $scheduledAppointments = Order::all();

        foreach ($scheduledAppointments as $order) {

            // Assuming $order->service_date is a valid date string
            $serviceDate = Carbon::parse($order->service_date, 'Asia/Manila');

            $now = Carbon::now('Asia/Manila');

            //Compare if now if today is greater than $service_date
            $compare = $now > $serviceDate;
            // Calculate the difference in days
            $dayDifference = $now->diffInDays($serviceDate);

            if ($order->service_date === $tomorrow && $order->status != 'Completed' && $order->service_type === 'Appointment' && $order->status != 'Cancelled') {
                $orderId = $order->id;
                $customerId = $order->user_id;
                $timeSlot = $order->time_slot->TimeSlot;
                $recipient = User::find($customerId);
                $recipients = User::whereIn('role_id', [1, 2])->get();

                Notification::make()
                ->title('Appointment alert.')
                ->body('Hello! Just a friendly reminder that you have an appointment scheduled for tomorrow. We\'re excited to see you at '.$timeSlot.'. See you soon!')
                ->warning()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/customer/orders/{$orderId}")
                ])
                ->sendToDatabase($recipient);

                Notification::make()
                ->title('Appointment tomorrow alert.')
                ->warning()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$orderId}")
                ])
                ->sendToDatabase($recipients);

            }elseif($compare && $dayDifference == 1 && $order->service_type === 'Appointment' && $order->status === 'Confirmed') {
                $orderId = $order->id;
                $customerId = $order->user_id;
                $timeSlot = $order->time_slot->TimeSlot;
                $recipient = User::find($customerId);
                $recipients = User::whereIn('role_id', [1, 2])->get();
                $order->status = 'Missed';
                $order->save();

                Notification::make()
                ->title('Missed appointment alert.')
                ->body('Hello! Unfortunately, you missed your appointment scheduled for earlier.')
                ->danger()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/customer/orders/{$orderId}")
                ])
                ->sendToDatabase($recipient);

                Notification::make()
                ->title('Missed appointment alert.')
                ->body('Hello! Unfortunately, a customer missed their appointment scheduled for earlier.')
                ->danger()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$orderId}")
                ])
                ->sendToDatabase($recipients);
            }elseif($order->status === 'Select payment method' && $order->service_date === $tomorrow){
                $orderId = $order->id;
                $customerId = $order->user_id;
                $recipient = User::find($customerId);

                $order->service_date = now()->addDays(3)->format('Y-m-d');
                $order->save();

                Notification::make()
                    ->title('Payment method reminder.')
                    ->body('Hello! Just a friendly reminder that you have an order scheduled for tomorrow, but you haven\'t selected a payment method yet. Your service date has been extended by 3 days. Please select a payment method at your earliest convenience.')
                    ->info()
                    ->actions([
                        NotifAction::make('view')
                            ->button()
                            ->url("/customer/orders/{$orderId}")
                    ])
                    ->sendToDatabase($recipient);
            }
        }
    }
}
