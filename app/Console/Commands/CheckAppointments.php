<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotifAction;

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

        //$scheduledAppointments = Appointment::whereDate('appointment_date', $tomorrow)->get();

        foreach ($scheduledAppointments as $order) {
            if ($order->service_date === $tomorrow && $order->status != 'Completed' && $order->service_type === 'Appointment') {
                $appointmentId = $order->id;
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
                    ->url("/customer/appointments/{$appointmentId}")
                ])
                ->sendToDatabase($recipient);

                Notification::make()
                ->title('Appointment tomorrow alert.')
                ->warning()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/appointments/{$appointmentId}")
                ])
                ->sendToDatabase($recipients);

            }elseif($order->service_date < now('Asia/Manila') && $appointment->status != 'Completed' && $order->service_type === 'Appointment') {
                $appointmentId = $order->id;
                $customerId = $order->user_id;
                $timeSlot = $appointment->time_slot->TimeSlot;
                $recipient = User::find($customerId);
                $recipients = User::whereIn('role_id', [1, 2])->get();
                $appointment->status = 'Missed';
                $appointment->save();

                Notification::make()
                ->title('Missed appointment alert.')
                ->body('Hello! Unfortunately, you missed your appointment scheduled for earlier.')
                ->danger()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/customer/appointments/{$appointmentId}")
                ])
                ->sendToDatabase($recipient);

                Notification::make()
                ->title('Missed appointment alert.')
                ->body('Hello! Unfortunately, a customer missed their appointment scheduled for earlier.')
                ->danger()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/appointments/{$appointmentId}")
                ])
                ->sendToDatabase($recipients);
            }
        }
    }
}
