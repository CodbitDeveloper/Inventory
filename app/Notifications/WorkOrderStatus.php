<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WorkOrderStatus extends Notification
{
    use Queueable;
    private $request;
    private $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($request, $message = null)
    {
        $this->request = $request;
        $this->message = $message == null ? 'Work Order with number #'.$request->wo_number.' ('.$request->title.') has been approved. You can now provide final report on the work order'
        : $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Work Order Status',
            'message' => $this->message,
            'data' => $this->request,
            'action' => '/'
        ];
    }
}
