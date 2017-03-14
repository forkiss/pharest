<?php

namespace Pharest\Validate;

use \Phalcon\Validation\Validator as Type;

class Validator extends \Phalcon\Validation
{
    public $input = [];

    protected $validators;

    protected $requires;

    protected $scopes;

    protected $lens;

    protected $notice;

    public function __construct(\Pharest\Config &$config)
    {
        $this->input = $this->request->get();

        $this->init();

        list($this->requires, $this->scopes, $this->lens) = $config->initValidatorRulers();
    }

    public function get($key, $default = null)
    {
        return $this->input[$key] ?? $default;
    }

    public function execute()
    {
        $this->rule($this->requires['keys'], new Type\PresenceOf($this->requires['detail']));

        $this->rule($this->scopes['keys'], new Type\InclusionIn($this->scopes['detail']));

        $this->rule($this->lens['keys'], new Type\StringLength($this->lens['detail']));

        $this->notice = $this->validate($this->input);

        if ($this->notice->valid()) {
            $exception = new \Pharest\Exception\ValidateException('表单验证失败');

            $exception->setNotice($this->notice);

            throw $exception;
        }
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

    public function required(string $key, string $message)
    {
        $this->requires['keys'][] = $key;
        $this->requires['detail']['message'][$key] = $message;

        return $this->get($key);
    }

    public function in(string $key, string $message, array $domain)
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

    private function init()
    {
        $this->filterXss($this->input);
    }
}