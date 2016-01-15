<?php

namespace Necktie\NewsletterBundle\Event;

use Symfony\Component\EventDispatcher\Event;


/**
 * Class NewsletterEvent.
 */
class NewsletterEvent extends Event
{
    /** @var  string */
    private $type;

    /** @var array */
    private $attributes;

    /** @var  string */
    private $result;

    /** @var  bool */
    private $hasError;


    public function __construct($type, $attributes = [])
    {
        $this->type = $type;
        $this->attributes = $attributes;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * @return array|string
     */
    public function getResult()
    {
        return $this->result;
    }


    /**
     * @param array $result
     */
    public function addResult($result)
    {
        $this->result = $result;
    }


    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }


    /**
     * @param bool $hasError
     */
    public function setError($hasError)
    {
        $this->hasError = $hasError;
    }
}
