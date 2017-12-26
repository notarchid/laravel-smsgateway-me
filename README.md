# SMSGateway.Me for Laravel 5.x
A PHP library to interact with [SMSGateway.Me](https://smsgateway.me) API.
## Installation
Run the following [composer](https://getcomposer.org) command to automatically update your `composer.json`
```bash
composer require notarchid/laravel-smsgateway-me
```
### Laravel 5.x:
After updating composer, add the ServiceProvider to the providers array in `config/app.php` so it can be loaded by Laravel.
```php
'providers' => [
    // ...
    NotArchid\SmsGateway\ServiceProvider::class,
],
```
In order to use the `SmsGateway` facade, you need to register it to the facades array in `config/app.php`.
```php
'facades' => [
    // ...
    'SmsGateway' => NotArchid\SmsGateway\Facade::class,
],
```
In order to use the `SmsGateway.Me`, you need to have an [SMSGateway.Me](https://smsgateway.me)'s account and set your credentials by inserting this inside your `config/services.php`:
```php
return [
    //...
    'smsgateway' => [
        'email' => 'your-email',
        'password' => 'your-password',
        'device_id' => 'your-device-id'
    ],
];
```
## Usage
```php
// Getting a list of your devices.
$sms = SmsGateway::devices()
                 ->get();
// Or 
$sms = SmsGateway::get('devices');

// Getting a list of your devices per page.
$page = 2;
$sms = SmsGateway::devices()
                 ->page($page)
                 ->get();

// Getting a details on a specific device.
$device_id = 33431;

$sms = SmsGateway::device($device_id)
                 ->get();
// Or 
$sms = SmsGateway::get('device', $device_id);

// Getting a list of your messages.
$sms = SmsGateway::messages()
                 ->get();
// Or 
$sms = SmsGateway::get('messages');

// Getting a list of your messages per page.
$page = 2;
$sms = SmsGateway::messages()
                 ->page($page)
                 ->get();

// Getting a details on s specific message.
$message_id = 82323;

$sms = SmsGateway::message($message_id)
                 ->get();
// Or
$sms = SmsGateway::get('message', $message_id);

// Sending message to a number.
$number = '+44771232343';
$message = 'Hello World!';

$sms = SmsGateway::to($number)
                 ->message($message)
                 ->send()
// Or
$sms = SmsGateway::send($number, $message);

// Sending message to many numbers.
$numbers = ['+44771232343', '+44771232344'];
$message = 'Hello World!';

$sms = SmsGateway::to($numbers)
                 ->message($message)
                 ->send();
// Or
$sms = SmsGateway::send($numbers, $message);

// Sending message to a contact.
$contact = 98123;
$message = 'Hello World!';

$sms = SmsGateway::contact($contact)
                 ->message($message)
                 ->send();
// Or
$sms = SmsGateway::send($contact, $message);

// Sending message to many contacts.
$contacts = [98123, 98124];
$message = 'Hello World!';

$sms = SmsGateway::contacts($contacts)
                 ->message($message)
                 ->send();
// Or
$sms = SmsGateway::send($contacts, $message);

// Sending many messages to many numbers or contacts.
$device1 = 33431;
$device2 = 33432;
$number = '+44771232343';
$contact = 98123;
$message1 = 'Hello there!';
$message2 = 'Good morning!';

$sms = SmsGateway::options([
                     [
                         'device' => $device1,
                         'number' => $number,
                         'message' => $message1,
                         'send_at' => strtotime('+1 minute'),
                         'expires_at' => strtotime('+10 minutes')
                     ], [
                         'device' => $device2,
                         'contact' => $number,
                         'message' => $message1,
                     ]
                 ])
                 ->send();
// Or
$sms = SmsGateway::send([
                     [
                         'device' => $device1,
                         'number' => $number,
                         'message' => $message1,
                         'send_at' => strtotime('+1 minute'),
                         'expires_at' => strtotime('+10 minutes')
                     ], [
                         'device' => $device2,
                         'contact' => $number,
                         'message' => $message1,
                     ]
                 ]);

// Getting a list of your contacts.
$sms = SmsGateway::contacts()
                 ->get();
// Or 
$sms = SmsGateway::get('contacts');

// Getting a list of your contacts per page.
$page = 2;
$sms = SmsGateway::contacts()
                 ->page($page)
                 ->get();

// Getting a details on a specific contact.
$contact_id = 33431;

$sms = SmsGateway::contact($contact_id)
                 ->get();
// Or 
$sms = SmsGateway::get('contact', $contact_id);

// Create a new contact.
$sms = SmsGateway::contact('John Doe', '+44771232343')
                 ->create();
// Or
$sms = SmsGateway::contact(['John Doe', '+44771232343'])
                 ->create();
// Or
$sms = SmsGateway::contact()
                 ->create('John Doe', '+44771232343');
// Or
$sms = SmsGateway::contact()
                 ->create(['John Doe', '+44771232343']);

// Adding options like send_at and expires_at is as simple as adding
// ->options(['send_at' => $timeToSend, 'expires_at' => $timeToExpire])
// to your command.
// Example:
$sms = SmsGateway::contact(98123)
                 ->message('Hello World!')
                 ->options([
                     'send_at' => strtotime('+1 minute'),
                     'expires_at' => strtotime('+10 minutes')
                 ])
                 ->send();
```
## License
MIT