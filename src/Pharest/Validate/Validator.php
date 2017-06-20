<?php

namespace Pharest\Validate;

use \Phalcon\Validation\Validator as Type;

class Validator extends \Phalcon\Validation
{

    protected $require;

    protected $scope;

    protected $len;

    protected $between;

    protected $multi;

    protected $match;

    public $input = [];

    public function __construct(\Pharest\Config &$config)
    {
        $this->input = $this->request->getJsonRawBody(true);

        $this->multi = $config->app->validate->multi;

        if ($config->app->validate->filter->get($config->method, false) and !empty($this->input)) {
            $this->filterXss($this->input);
        }

        $this->require = $this->scope = $this->between = $this->len = $this->match = [
            'keys'   => [],
            'detail' => ['cancelOnFail' => !$this->multi]
        ];
    }

    public function get($key, $default = null)
    {
        return $this->input[$key] ?? $default;
    }

    public function execute()
    {
        $this->add($this->require['keys'], new Type\PresenceOf($this->require['detail']));

        $this->add($this->scope['keys'], new Type\InclusionIn($this->scope['detail']));

        $this->add($this->len['keys'], new Type\StringLength($this->len['detail']));

        $this->add($this->between['keys'], new Type\Between($this->between['detail']));

        $this->add($this->match['keys'], new Type\Regex($this->match['detail']));

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
        $this->appendPresence($key, $message);

        return $this->get($key);
    }

    public function inclusion(string $key, array $domain, string $message)
    {
        $this->appendInclusion($key, $domain, $message);

        return $this->get($key);
    }

    public function length(string $key, int $min, string $messageMinimum, int $max = null, string $messageMaximum = null)
    {
        $this->appendLength($key, $min, $messageMinimum, $max, $messageMaximum);

        return $this->get($key);
    }

    public function between(string $key, float $minimum, float $maximum, string $message)
    {
        $this->appendBetween($key, $minimum, $maximum, $message);

        return $this->get($key);
    }

    public function match(string $key, string $pattern, string $message)
    {
        $this->appendMatch($key, $pattern, $message);

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

    public function appendBetween(string $key, float $minimum, float $maximum, string $message)
    {
        $this->between['keys'][] = $key;

        $this->between['detail']['minimum'][$key] = $minimum;
        $this->between['detail']['maximum'][$key] = $maximum;
        $this->between['detail']['message'][$key] = $message;
    }

    public function appendMatch(string $key, string $pattern, string $message)
    {
        $this->match['keys'][] = $key;
        $this->match['detail']['message'][$key] = $message;
        $this->match['detail']['pattern'][$key] = $pattern;
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

    public function filterSecurityString($string, $len = 50)
    {
        $string = mb_substr($string, 0, $len, 'UTF-8');

        return str_replace(["\r", "\r\n", "\n", "\t", '"', "'", " ", "&nbsp;", "\v", "\xe2\x80\xa8", "&nbs", ' '], ['', '', '', '', '“', '‘', " ", '', '', '', '', ''], $string);
    }

}