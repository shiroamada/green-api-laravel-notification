<?php

namespace NotificationChannels\GreenApi;

use Illuminate\Notifications\Notification;
use NotificationChannels\GreenApi\Exceptions\CouldNotSendNotification;

class GreenApiChannel
{
    /** @var \NotificationChannels\GreenApi\GreenApi */
    protected $greenApi;

    public function __construct(GreenApi $greenApi)
    {
        $this->greenApi = $greenApi;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @throws  \NotificationChannels\GreenApi\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('green_api');

        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toGreenApi($notifiable);

        if (is_string($message)) {
            $message = new GreenApiMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    protected function sendMessage($recipient, GreenApiMessage $message)
    {
        //$message->content = html_entity_decode($message->content, ENT_QUOTES, 'utf-8');
        //$message->content = urlencode($message->content);

        //clean the recipient
        $recipient = str_replace("-", "", $recipient);
        $recipient = str_replace(" ", "", $recipient);

        $valid_mobile = '';

        //debug mode is to avoid send whatsapp to your real customer
        if ($this->greenApi->isDebug)
        {
            $valid_mobile = $this->greenApi->debugReceiveNumber;
        }
        else
        {
            if($this->greenApi->isMalaysiaMode)
            {
                //this is for malaysia number use case,
                if ($recipient[0] == '6')
                {
                    $valid_mobile = '+' . $recipient;
                }

                if ($recipient[0] == '0')
                {
                    $valid_mobile = '+6' . $recipient;
                }
            }
            else
            {
                //please set +[CountryCode]
                $valid_mobile = $recipient;
            }
        }

        $params = [
            'to'        => $valid_mobile,
            'mesg'      => $message->content,
        ];

        if ($message->sendAt instanceof \DateTimeInterface) {
            $params['time'] = '0'.$message->sendAt->getTimestamp();
        }

        $this->greenApi->send($params);
    }
}
