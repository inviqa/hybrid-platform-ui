<?php

namespace EzSystems\HybridPlatformUi\Decorator;

use eZ\Publish\API\Repository\Values\ValueObject;

abstract class ValueObjectDecorator
{
    /**
     * @var ValueObjectDecorator
     */
    protected $valueObject;

    public function __construct(ValueObject $valueObject)
    {
        $this->valueObject = $valueObject;
    }

    public function getValueObject()
    {
        return $this->valueObject;
    }

    public function __set($property, $value)
    {
        return $this->valueObject->__set($property, $value);
    }

    public function __get($property)
    {
        return $this->valueObject->__get($property);
    }

    public function __isset($property)
    {
        return $this->valueObject->__isset($property);
    }

    public function __unset($property)
    {
        return $this->valueObject->__unset($property);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->valueObject, $method], $args);
    }
}