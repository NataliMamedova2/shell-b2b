<?php

namespace CrudBundle\Action;

final class Response implements \CrudBundle\Interfaces\Response
{
    /**
     * @var mixed
     */
    private $data = [];

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var array
     */
    private $errors = [];

    public function __construct(array $data)
    {
        $state = get_object_vars($this);

        $stateIntersect = array_intersect_key($data, $state);

        foreach ($stateIntersect as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    public function getErrors(): array
    {
        return (array) $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
