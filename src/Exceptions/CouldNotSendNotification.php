<?php

namespace NotificationChannels\GreenApi\Exceptions;

use DomainException;
use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when recipient's phone number is missing.
     *
     * @return static
     */
    public static function missingRecipient()
    {
        return new static('Notification was not sent. Phone number is missing.');
    }

    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded()
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }

    /**
     * Thrown when we're unable to communicate with green-api.
     *
     * @param  DomainException  $exception
     *
     * @return static
     */
    public static function exceptionGreenApiRespondedWithAnError(DomainException $exception)
    {
        return new static(
            "Green Api responded with an error '{$exception->getCode()}: {$exception->getMessage()}'"
        );
    }

    /**
     * Thrown when we're unable to communicate with green-api.
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithGreenApi(Exception $exception)
    {
        return new static("The communication with green api failed. Reason: {$exception->getMessage()}");
    }
}
