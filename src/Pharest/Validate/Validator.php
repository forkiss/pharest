<?php

namespace Pharest\Validate;

use \Phalcon\Validation\Validator as Type;

class Validator extends \Phalcon\Validation
{
    public $input = [];

    protected $requires;

    protected $scopes;

    protected $lens;

    public function __construct(\Pharest\Config &$config)
    {
        $this->input = $this->request->get();

        $this->filterXss($this->input);

        list($this->requires, $this->scopes, $this->lens) = $config->initValidatorRulers();
    }

    public function get($key, $default = null)
    {
        return $this->input[$key] ?? $default;
    }

    public function execute($errorMessage = 'validate fail')
    {
        $this->rule($this->requires['keys'], new Type\PresenceOf($this->requires['detail']));

        $this->rule($this->scopes['keys'], new Type\InclusionIn($this->scopes['detail']));

        $this->rule($this->lens['keys'], new Type\StringLength($this->lens['detail']));

        $notice = $this->validate($this->input);

        if ($notice->valid()) {
            $exception = new \Pharest\Exception\ValidateException($errorMessage);

            $exception->setNotice($notice);

            throw $exception;
        }
    }

    public function presence(string $key, string $message)
    {
        $this->requires['keys'][] = $key;
        $this->requires['detail']['message'][$key] = $message;

        return $this->get($key);
    }

    public function inclusion(string $key, string $message, array $domain)
    {
        $this->scopes['keys'][] = $key;
        $this->scopes['detail']['message'][$key] = $message;
        $this->scopes['detail']['domain'][$key] = $domain;

        return $this->get($key);
    }

    public function length(string $key, int $min, string $messageMinimum, int $max = null, string $messageMaximum = null)
    {
        $this->lens['keys'][] = $key;

        $this->lens['detail']['min'][$key] = $min;
        $this->lens['detail']['messageMinimum'][$key] = $messageMinimum;

        if ($max and $messageMaximum) {
            $this->lens['detail']['max'][$key] = $max;
            $this->lens['detail']['messageMaximum'][$key] = $messageMaximum;
        }

        return $this->get($key);
    }

    public function appendPresence(string $key, string $message)
    {
        $this->requires['keys'][] = $key;
        $this->requires['detail']['message'][$key] = $message;
    }

    public function appendInclusion(string $key, string $message, array $domain)
    {
        $this->scopes['keys'][] = $key;
        $this->scopes['detail']['message'][$key] = $message;
        $this->scopes['detail']['domain'][$key] = $domain;
    }

    public function appendLength(string $key, int $min, string $messageMinimum, int $max = null, string $messageMaximum = null)
    {
        $this->lens['keys'][] = $key;

        $this->lens['detail']['min'][$key] = $min;
        $this->lens['detail']['messageMinimum'][$key] = $messageMinimum;

        if ($max and $messageMaximum) {
            $this->lens['detail']['max'][$key] = $max;
            $this->lens['detail']['messageMaximum'][$key] = $messageMaximum;
        }
    }

    private function filterXss(array &$data)
    {
        foreach ($data as $key => $value) {

            if (!is_scalar($value)) {
                continue;
            }

            $data[$key] = trim(strip_tags($value));

            $data[$key] = preg_replace(['/[on][a-zA-Z]+(\s*)=(\s*)?[\'"]?[^\'"]+[\'"&gt;]?/i', '/>/'], '', $data[$key]);
        }
    }
}