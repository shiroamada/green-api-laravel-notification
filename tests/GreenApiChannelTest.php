<?php

namespace NotificationChannels\GreenApi\Test;

use Illuminate\Notifications\Notification;
use Mockery as M;
use NotificationChannels\GreenApi\Exceptions\CouldNotSendNotification;
use NotificationChannels\GreenApi\GreenApi;
use NotificationChannels\GreenApi\GreenApiChannel;
use NotificationChannels\GreenApi\GreenApiMessage;
use PHPUnit\Framework\TestCase;

class GreenApiChannelTest extends TestCase
{
    /**
     * @var greenApi
     */
    private $greenApi;

    /**
     * @var GreenApiMessage
     */
    private $message;

    /**
     * @var GreenApiChannel
     */
    private $channel;

    /**
     * @var \DateTime
     */
    public static $sendAt;

    public function setUp(): void
    {
        parent::setUp();

        $config = [
            'instanceId'=> 'instaceId',
            'token'     => 'token',
        ];

        $this->greenApi = M::mock(GreenApi::class, $config);
        $this->channel = new GreenApiChannel($this->greenApi);
        $this->message = M::mock(GreenApiMessage::class);
    }

    public function tearDown(): void
    {
        M::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->greenApi->shouldReceive('send')->once()
            ->with(
                [
                    'to'  => '60123456789',
                    'body'     => 'hello',
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_deferred_notification()
    {
        self::$sendAt = new \DateTime();

        $this->greenApi->shouldReceive('send')->once()
            ->with(
                [
                    'to'  => '60123456789',
                    'body'     => 'hello',
                    'time'    => '0'.self::$sendAt->getTimestamp(),
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotificationWithSendAt());
    }

    /** @test */
    public function it_does_not_send_a_message_when_to_missed()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send(
            new TestNotifiableWithoutRouteNotificationForSmscru(), new TestNotification()
        );
    }
}

class TestNotifiable
{
    public function routeNotificationFor()
    {
        return '0123456789';
    }
}

class TestNotifiableWithoutRouteNotificationForSmscru extends TestNotifiable
{
    public function routeNotificationFor()
    {
        return false;
    }
}

class TestNotification extends Notification
{
    public function toGreenApi()
    {
        return GreenApiMessage::create('hello');
    }
}

class TestNotificationWithSendAt extends Notification
{
    public function toGreenApi()
    {
        return GreenApiMessage::create('hello')
            ->sendAt(GreenApiChannelTest::$sendAt);
    }
}
