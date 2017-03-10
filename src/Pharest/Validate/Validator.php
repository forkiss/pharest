<?php

namespace Pharest\Validate;

use \Phalcon\Validation\Validator as Type;

class Validator extends \Phalcon\Validation
{
    protected $input;

    protected $validators;

    protected $requires;

    protected $scopes;

    protected $lens;

    protected $notice;

    public final function initialize()
    {
        $method = $this->request->getMethod();

        if ($method == 'PUT') {
            $this->input = $this->request->getPut();
        } elseif ($this->method == 'POST') {
            $this->input = $this->request->getPost();
        } else {
            $this->input = [];
        }

        if (!empty($this->input)) {
            $this->filterXss($this->input);
        }

        $this->scopes = ['keys' => [], 'detail' => ['message' => [], 'domain' => []]];
        $this->lens = ['keys' => [], 'detail' => ['min' => [], 'messageMinimum' => [], 'max' => [], 'messageMaximum' => []]];
    }

    public function get($key)
    {
        return $this->input[$key] ?? null;
    }

    public function required(string $key, string $message)
    {
        $this->required[$key] = [$key => $message];

        return $this->get($key);
    }

    public function in(string $key, string $message, array $domain)
    {
        $this->scopes['keys'][] = $key;
        $this->scopes['detail']['message'][$key] = $message;
        $this->scopes['detail']['domain'][$key] = $domain;

        $this->add($key, new Type\InclusionIn([
            'message' => $message,
            'domain'  => $domain
        ]));
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

    public function execute()
    {
        if ($this->requires) {
            $this->add(array_keys($this->requires), new Type\PresenceOf(['message' => array_values($this->requires)]));
        }

        if ($this->scopes) {
            $this->add($this->scopes['keys'], new Type\InclusionIn($this->scopes['detail']));
        }

        if ($this->lens) {
            $this->add($this->lens['keys'], new Type\StringLength($this->lens['detail']));
        }

        $this->notice = $this->validate($this->input);

        if ($this->notice->count()) {
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
}