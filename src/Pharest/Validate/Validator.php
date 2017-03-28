<?php

namespace Pharest\Validate;

use \Phalcon\Validation\Validator as Type;

class Validator extends \Phalcon\Validation
{
    public $input = [];

    protected $multi;

    protected $require;

    protected $scope;

    protected $len;

    protected $between;

    public function __construct(bool &$multi)
    {
        $this->multi = $multi;

        $this->require = $this->scope = $this->len = $this->between = ['keys' => [], 'detail' => ['cancelOnFail' => !$multi]];

        $this->input = $this->request->get();

        $this->filterXss($this->input);
    }

    public function get($key, $default = null)
    {
        return $this->input[$key] ?? $default;
    }

    public function execute()
    {
        $this->rule($this->require['keys'], new Type\PresenceOf($this->require['detail']));

        $this->rule($this->scope['keys'], new Type\InclusionIn($this->scope['detail']));

        $this->rule($this->len['keys'], new Type\StringLength($this->len['detail']));

        $this->rule($this->between['keys'], new Type\Between($this->between['detail']));

        $notice = $this->validate($this->input);

        if ($notice->valid()) {

            if (!$this->multi) {
                throw new \Pharest\Exception\ValidateException($notice->current()->getMessage());
            }

            $exception = new \Pharest\Exception\ValidateException('params invalid');

            $exception->setNotice($notice);

            throw $exception;
        }
    }

    public function presence(string $key, string $message)
    {
        $this->require['keys'][] = $key;
        $this->require['detail']['message'][$key] = $message;

        return $this->get($key);
    }

    public function inclusion(string $key, array $domain, string $message)
    {
        $this->scope['keys'][] = $key;
        $this->scope['detail']['message'][$key] = $message;
        $this->scope['detail']['domain'][$key] = $domain;

        return $this->get($key);
    }

    public function length(string $key, int $min, string $messageMinimum, int $max = null, string $messageMaximum = null)
    {
        $this->len['keys'][] = $key;

        $this->len['detail']['min'][$key] = $min;
        $this->len['detail']['messageMinimum'][$key] = $messageMinimum;

        if ($max and $messageMaximum) {
            $this->len['detail']['max'][$key] = $max;
            $this->len['detail']['messageMaximum'][$key] = $messageMaximum;
        }

        return $this->get($key);
    }

    public function between(string $key, float $minimum, float $maximum, string $message)
    {
        $this->between['keys'][] = $key;

        $this->between['detail']['minimum'][$key] = $minimum;
        $this->between['detail']['maximum'][$key] = $maximum;
        $this->between['detail']['message'][$key] = $message;

        return $this->get($key);
    }

    public function appendPresence(string $key, string $message)
    {
        $this->require['keys'][] = $key;
        $this->require['detail']['message'][$key] = $message;
    }

    public function appendInclusion(string $key, array $domain, string $message)
    {
        $this->scope['keys'][] = $key;
        $this->scope['detail']['message'][$key] = $message;
        $this->scope['detail']['domain'][$key] = $domain;
    }

    public function appendLength(string $key, int $min, string $messageMinimum, int $max = null, string $messageMaximum = null)
    {
        $this->len['keys'][] = $key;

        $this->len['detail']['min'][$key] = $min;
        $this->len['detail']['messageMinimum'][$key] = $messageMinimum;

        if ($max and $messageMaximum) {
            $this->len['detail']['max'][$key] = $max;
            $this->len['detail']['messageMaximum'][$key] = $messageMaximum;
        }
    }

    public function appendBetween(string $key, int $minimum, int $max, string $message)
    {
        $this->between['keys'][] = $key;

        $this->between['detail']['minimum'][$key] = $minimum;
        $this->between['detail']['maximum'][$key] = $max;
        $this->between['detail']['message'][$key] = $message;
    }

    public function filterXss(array &$data)
    {
        foreach ($data as $key => $value) {

            if (!is_scalar($value)) {
                continue;
            }

            $data[$key] = trim(strip_tags($value));

            $data[$key] = preg_replace(['/[on][a-zA-Z]+(\s*)=(\s*)?[\'"]?[^\'"]+[\'"&gt;]?/i', '/>/'], '', $data[$key]);
        }
    }

    public function filterSecurityString($string, $len = 45)
    {
        $string = mb_substr($string, 0, $len, 'UTF-8');

        return str_replace(["\r", "\r\n", "\n", "\t", '"', "'", " ", "&nbsp;", "\v", "\xe2\x80\xa8", "&nbs", ' '], ['', '', '', '', '“', '‘', " ", '', '', '', '', ''], $string);
    }

}