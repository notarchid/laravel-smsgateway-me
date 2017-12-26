<?php

namespace NotArchid\SmsGateway;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * SmsGateway.Me for Laravel
 *
 * @package laravel-smsgateway-me
 */
class SmsGateway
{
    const DEFAULT_PAGE = 1;

    /**
     * Base url.
     * 
     * @var string
     */
    protected $url = 'https://smsgateway.me/api/v3';

    /**
     * GuzzleHttp Client.
     * 
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Your SMSGateway.Me account's email address.
     * 
     * @var string
     */
    protected $email;

    /**
     * Your SMSGateway.Me account's password.
     * 
     * @var string
     */
    protected $password;

    /**
     * The ID of the device you wish to send the message
     * from.
     * 
     * @var int
     */
    protected $device_id;

    /**
     * The phone number(s) to send the message to.
     * 
     * @var string|array
     */
    protected $to;

    /**
     * The contact(s) to send the message to.
     * 
     * @var int|array
     */
    protected $contact;

    /**
     * The content of the message to be sent.
     * 
     * @var string|array
     */
    protected $message;

    /**
     * The page number (500 results per page).
     * 
     * @var int
     */
    protected $page;

    /**
     * The type of list or details you want to get from
     * SMSGateway.Me
     * 
     * @var string
     */
    protected $type;

    /**
     * Additional options.
     * 
     * @var array
     */
    protected $options = [];
    
    /**
     * SmsGateway accept an array of constructor parameters.
     * 
     * @param \GuzzleHttp\Client $client
     * @param string $email
     * @param string $password 
     * @param int $device_id
     */
    function __construct(Client $client, $email, $password, $device_id)
    {
        $this->client = $client;
        $this->email = $email;
        $this->password = $password;
        $this->device_id = $device_id;
    }

    /**
     * Set $device_id.
     *
     * $sms->device(5)
     *     ->send('+44771232343', 'Hello World!');
     * 
     * @param  int $device_id
     * @return self
     */
    public function device($device_id)
    {
        $this->device_id = $device_id;

        if (! is_int($device_id))
            throw new Exception('Device ID should be of int type.');

        return $this;
    }

    /**
     * Set $type to 'devices' (Used for get request).
     *
     * Example:
     * $sms->devices()
     *     ->get();
     * 
     * @return self
     */
    public function devices()
    {
        $this->type = 'devices';

        return $this;
    }

    /**
     * Set $to.
     *
     * Example:
     * $sms->to(['+44771232343', '+44771232344'])
     *     ->message('Hello World!')
     *     ->send();
     * 
     * @param  string|array $to
     * @return self
     */
    public function to($to)
    {
        $this->to = $to;

        if (is_array($to)) {
            if (count($to) === 1)
                $this->to = implode($to);

            foreach ($to as $arr) {
                if (! is_string($arr))
                    throw new Exception('Number should be of string type.');
            }
        }

        return $this;
    }

    /**
     * Set $contact.
     *
     * Example:
     * $sms->contact(4)
     *     ->message('Hello World!')
     *     ->send();
     * 
     * @param  int $contact
     * @return self
     */
    public function contact($contact = null, $number = null)
    {
        if (is_array($contact)) {
            foreach ($contact as $arr) {
                if (! is_string($arr))
                    throw new Exception('Contact\'s Name and Number should be of string type.');
            }
        }

        $this->contact = $contact;

        if (! empty($number))
            $this->contact = [$contact, $number];

        $this->type = empty($contact) || is_array($contact) || is_string($contact) ? 'create' : null;

        return $this;
    }

    /**
     * Set $contact and/or $type to 'contacts' (Used for
     * get request).
     *
     * Example:
     * $sms->contacts([1, 2, 3, 4, 5])
     *     ->message('Hello World!')
     *     ->send();
     * 
     * @param  array $contacts
     * @return self
     */
    public function contacts($contacts = null)
    {
        $this->contact = $contacts;

        if (is_array($contacts)) {
            foreach ($contacts as $contact) {
                if (! is_int($contact))
                    throw new Exception('Contact ID should be of int type.');
            }
        }

        $this->type = $contacts ?: 'contacts';

        return $this;
    }

    /**
     * Set $message.
     *
     * Example:
     * $sms->device(2)
     *     ->contacts([1, 5, 3])
     *     ->message('Hello World!')
     *     ->send();
     * 
     * @param  string $message
     * @return self
     */
    public function message($message = null)
    {
        if (empty($message))
            throw new Exception('Too few arguments.');
            
        $this->message = $message;

        return $this;
    }

    /**
     * Set $message and/or $type to 'messages' (Used for
     * get request).
     *
     * Example:
     * $sms->messages()
     *     ->page(5)
     *     ->get();
     * 
     * @param  array $messages
     * @return self
     */
    public function messages()
    {
        $this->type = 'messages';

        return $this;
    }

    /**
     * Set $page.
     *
     * Example:
     * $sms->devices()
     *     ->page(5)
     *     ->get();
     * 
     * @param  int $page
     * @return self
     */
    public function page($page = DEFAULT_PAGE)
    {           
        $this->page = $page;

        return $this;
    }

    /**
     * Set $options.
     *
     * Example:
     * $sms->options([
     *         [
     *             'device' => 1,
     *             'number' => '+44771232343',
     *             'message' => 'Hello World!',
     *             'send_at' => strtotime('+10 minutes'),
     *             'expires_at' => strtotime('+1 hour')
     *         ], [
     *             'device' => 2,
     *             'contact' => 2,
     *             'message' => 'Aloha, World!'
     *         ]
     *     ])
     *     ->send();
     * 
     * @param  array $options
     * @return self
     */
    public function options($options = null)
    {
        if (empty($options))
            throw new Exception('Too few arguments.');
        else if (! is_array($options))
            throw new Exception('Too few arguments.');
        else {
            if (array_key_exists('number', $options) || array_key_exists('contact', $options) || array_key_exists('message', $options))
                throw new Exception('Too many arguments.');
        }
            
        $this->options = $options;

        return $this;
    }

    /**
     * Create new contact.
     *
     * Example:
     * $sms->contact()
     *     ->create('John Doe', '+44771232343');
     * 
     * @param  string|array $name
     * @param  string $number
     * @return array
     */
    public function create($name = '', $number = '')
    {
        $contact = $this->contact ? $this->contact : null;

        if ($this->type !== 'create')
            throw new Exception('Unknown operation.');

        if (empty($name)) {
            if (empty($contact) || ! is_array($contact))
                throw new Exception('Too few arguments.');

            $fields['name'] = $contact[0];
            $fields['number'] = $contact[1];
        } else {
            if (is_array($name)) {
                if (count($name) !== 2)
                    throw new Exception('Too many arguments.');
                
                $fields['name'] = $name[0];
                $fields['number'] = $name[1];
            } else {
                if (empty($number))
                    throw new Exception('Too few arguments.');

                if (! is_string($name) || ! is_string($number))
                    throw new Exception('Name and Number should be of string type.');
                    
                
                $fields['name'] = $name;
                $fields['number'] = $number;
            }
        }

        $this->url .= '/contacts/create';

        $fields = array_merge([
            'email' => $this->email,
            'password' => $this->password,
        ], $fields);

        $request = $this->client->request('POST', $this->url, [
            'form_params' => $fields,
            'http_errors' => false
        ]);

        $result['response'] = json_decode($request->getBody());

        if ($result['response'] == false)
            $result['response'] = $request;

        $result['status'] = $request->getStatusCode();

        return $result;
    }

    /**
     * Send message.
     *
     * Example:
     * $sms->send('+44771232343', 'Hello World!');
     * 
     * @param  string|array|int $to
     * @param  string $message
     * @return array
     */
    public function send($to = '', $message = '')
    {
        $options = $this->options;
        $device = $this->device_id;

        if (empty($to)) {
            $fields['number'] = ! empty($this->to) ? $this->to : null;
            $fields['contact'] = ! empty($this->contact) ? $this->contact : null;
            $fields['message'] = ! empty($this->message) ? $this->message : null;

            if (! empty($options) && is_array($options)) {
                if (empty($fields['number']) && empty($fields['contact']) && empty($fields['message'])) {
                    foreach ($options as $option) {
                        if (is_array($option) && ! array_key_exists('number', $option) && ! array_key_exists('contact', $options) && ! array_key_exists('message', $option))
                            throw new Exception('Too few arguments.');
                        else if (is_array($option) && ! array_key_exists('number', $option) && ! array_key_exists('contact', $option))
                            throw new Exception('Too few arguments.');
                        else if (is_array($option) && array_key_exists('number', $option) && array_key_exists('contact', $option))
                            throw new Exception('Too many arguments.');
                        else if (! is_array($option))
                            throw new Exception('Too few arguments.');
                    }

                    $fields = [];
                    $fields['data'] = $options;
                    $options = [];
                    $device = null;
                } else {
                    foreach ($options as $option) {
                        if (is_array($option) && (array_key_exists('number', $option) || array_key_exists('contact', $options) || array_key_exists('message', $option)))
                            throw new Exception('Too many arguments.');
                    }
                }
            }

            if (empty($fields['number']))
                unset($fields['number']);
            else if (empty($fields['contact']))
                unset($fields['contact']);
        } else {
            if (is_array($to)) {
                if (is_array($to[0])) {
                    if (! empty($message))
                        throw new Exception('Too many arguments.');
                    
                    foreach ($to as $arr) {
                        if (is_array($arr) && (! array_key_exists('number', $arr) && ! array_key_exists('contact', $arr)) && ! array_key_exists('message', $arr))
                            throw new Exception('Too few arguments.');
                        else if (is_array($options)) {
                            foreach ($options as $option) {
                                if (is_array($option) && (array_key_exists('number', $option) || array_key_exists('contact', $option) || array_key_exists('message', $option)))
                                    throw new Exception('Too many arguments.');
                            }
                        }
                    }

                    $fields['data'] = $to;
                    $device = null;
                } else {
                    if (empty($message))
                        throw new Exception('Too few arguments.');
                        
                    if (! is_string($message))
                        throw new Exception('Message should be of string type.');

                    if (count($to) > 1) {
                        if (is_string($to[0]))
                            $fields['number'] = $to;
                        else
                            $fields['contact'] = $to;
                    } else {
                        if (is_string($to[0]))
                            $fields['number'] = implode($to);
                        else
                            $fields['contact'] = implode($to);
                    }

                    $fields['message'] = $message;
                }
            } else {
                if (empty($message))
                    throw new Exception('Too few arguments.');

                if (! is_string($message))
                    throw new Exception('Message should be of string type.');

                if (is_string($to))
                    $fields['number'] = $to;
                else
                    $fields['contact'] = $to;

                $fields['message'] = $message;
            }
        }

        $this->url .= '/messages/send';

        $fields = array_merge([
            'email' => $this->email,
            'password' => $this->password,
        ], $options, $fields);

        if (! empty($device))
            $fields = array_merge($fields, ['device' => $device]);

        $request = $this->client->request('POST', $this->url, [
            'form_params' => $fields,
            'http_errors' => false
        ]);

        $result['response'] = json_decode($request->getBody());

        if ($result['response'] == false)
            $result['response'] = $request;

        $result['status'] = $request->getStatusCode();

        return $result;
    }

    /**
     * Get list or details of device|contact|message
     * from SMSGateway.Me.
     *
     * Example:
     * $sms->get('message', 1);
     * 
     * @param  string $type
     * @param  int $id
     * @return array
     */
    public function get($type = '', $id = null)
    {
        $type = ! empty($type) ? $type : $this->type;
        $id = ! empty($id) ? $id : null;

        if (empty($this->device_id) && empty($this->contact)  && empty($this->message) && empty($type))
            throw new Exception('Too few arguments.');

        if (! empty($type) && ! is_string($type))
            throw new Exception('Unknown type.');

        if (! empty($id) && ! is_int($id))
            throw new Exception('Unknown ID.');

        switch ($type) {
            case 'devices':
                $this->url .= '/devices';
                break;

            case 'contacts':
                $this->url .= '/contacts';
                break;

            case 'messages':
                $this->url .= '/messages';
                break;

            case 'device':
                $this->url .= '/devices/view/' . $id;
                break;

            case 'contact':
                $this->url .= '/contacts/view/' . $id;
                break;

            case 'message':
                $this->url .= '/messages/view/' . $id;
                break;

            default:
                $id = ! empty($id) ? $id : null;

                if (is_int($this->device_id)) {
                    $id = $this->device_id;
                    
                    $this->url .= '/devices/view/' . $id;
                } else if (is_int($this->contact)) {
                    $id = $this->contact;

                    $this->url .= '/contacts/view/' . $id;
                } else if (is_int($this->message)) {
                    $id = $this->message;

                    $this->url .= '/messages/view/' . $id;
                } else
                    throw new Exception('Too few arguments.');

                break;
        }

        $fields = [];

        if (in_array($type, ['devices', 'contacts', 'messages'])) {
            $fields = array_merge([
                'page' => ($this->page ? $this->page : self::DEFAULT_PAGE)
            ], $fields);
        }

        $fields = array_merge([
            'email' => $this->email,
            'password' => $this->password
        ], $fields);

        $request = $this->client->request('GET', $this->url, [
            'query' => $fields,
            'http_errors' => false
        ]);

        $result['response'] = json_decode($request->getBody());

        if ($result['response'] == false)
            $result['response'] = $request;

        $result['status'] = $request->getStatusCode();

        return $result;
    }
}