# GREEN API notifications channel for Laravel 5.3+


[![Latest Version on Packagist](https://img.shields.io/packagist/v/shiroamada/green-api.svg?style=flat-square)](https://packagist.org/packages/shiroamada/green-api-laravel-notification)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/shiroamada/green-api-laravel-notification/master.svg?style=flat-square)](https://travis-ci.org/shiroamada/gosms)
[![StyleCI](https://styleci.io/repos/108503043/shield)](https://styleci.io/repos/108503043)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/smsc-ru.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/green-api-laravel-notification)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/smsc-ru/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/shiroamada/green-api-laravel-notification/?branch=main)
[![Total Downloads](https://img.shields.io/packagist/dt/shiroamada/green-api-laravel-notification.svg?style=flat-square)](https://packagist.org/packages/shiroamada/green-api-laravel-notification)

This package makes it easy to send notifications using [https://green-api.com/en/](https://green-api.com/en) with Laravel 5.3+.

Code Reference from laravel-notification-channels/smsc-ru

## Contents

- [Installation](#installation)
    - [Setting up the Green API service](#setting-up-the-green-api-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require shiroamada/green-api-laravel-notification
```

Then you must install the service provider:
```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\GreenApi\GreenApiServiceProvider::class,
],
```

### Setting up the Green API service

Add your green api instanceId and token to your `config/services.php`:

```php
// config/services.php
...
'green_api' => [
    'instanceId' => env('GREENAPI_INSTANCEID'),
    'token' => env('GREENAPI_TOKEN'),
    'isMalaysiaMode' => env('GREENAPI_MALAYSIA_MODE') ?? 0,
    'isDebug' => env('GREENAPI_DEBUG_ENABLE') ?? 0,
    'debugReceiveNumber' => env('GREENAPI_DEBUG_RECEIVE_NUMBER'),
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\GreenApi\GreenApiMessage;
use NotificationChannels\GreenApi\GreenApiChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [GreenApiChannel::class];
    }

    public function toGreenApi($notifiable)
    {
        return GreenApiMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a routeNotificationForGreenApi() method, which return the phone number.

```php
public function routeNotificationForGreenApi()
{
    return $this->mobile; //depend what is your db field
}
```

### Available methods

`content()`: Set a content of the notification message.

To send a next new line message, in PHP please use double quote "\n"

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please use the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [ShiroAmada](https://github.com/shiroamada)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
