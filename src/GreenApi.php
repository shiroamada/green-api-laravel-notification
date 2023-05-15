<?php

namespace NotificationChannels\GreenApi;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\GreenApi\Exceptions\CouldNotSendNotification;

class GreenApi
{
    /** @var string */
    protected $apiUrl = 'https://api.green-api.com/';

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $instanceId;

    /** @var string */
    protected $token;

    /** @var integer */
    public $isMalaysiaMode;

    /** @var integer */
    public $isEnable;

    /** @var integer */
    public $isDebug;

    /** @var string */
    public $debugReceiveNumber;


    /** @var string */
    protected $action = '/SendMessage/';

    /** @var string */
    protected $priority = '10';


    public function __construct($config)
    {
        $this->instanceId = $config['instanceId'];
        $this->token = $config['token'];

        $this->isMalaysiaMode = $config['isMalaysiaMode'];
        $this->isEnable = (isset($config['isEnable']) ? $config['isEnable'] : 0);
        $this->isDebug = $config['isDebug'];
        $this->debugReceiveNumber = $config['debugReceiveNumber'];


        //POST https://api.green-api.com/waInstance{{idInstance}}/SendMessage/{{apiTokenInstance}}
        $this->httpClient = new HttpClient([
            'base_uri' =>  $this->apiUrl.'waInstance'.$this->instanceId.$this->action,
            'timeout' => 8.0,
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
    }

    /**
     * @param  array  $params
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        if($this->isEnable)
        {
            try
            {

                $response = $this->httpClient->post(
                    $this->apiUrl . 'waInstance' . $this->instanceId . $this->action . $this->token,
                    [
                        'json' => [
                            'chatId'  => $params['to'] . '@c.us',
                            'message' => $params['mesg'],
                        ]
                    ]);

                $stream = $response->getBody();

                $content = $stream->getContents();

                $response = json_decode((string) $response->getBody(), true);

                return $response;
            } catch (DomainException $exception)
            {
                throw CouldNotSendNotification::exceptionGreenApiRespondedWithAnError($exception);
            } catch (\Exception $exception)
            {
                throw CouldNotSendNotification::couldNotCommunicateWithGreenApi($exception);
            }
        }
    }
}
