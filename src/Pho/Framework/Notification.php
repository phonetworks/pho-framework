<?php

namespace Pho\Framework;

class Notification
{
    const MSG = "%s has mentioned %s";

    /**
     * Parameters to complement self::MSG
     *
     * @var array
     */
    protected $params;

    public function __construct(...$params)
    {
        $this->params = $params;
    }

    public function __toString(): string
    {
        return sprintf(static::MSG, ...$this->params);
    }

   public function toArray(): array
   {

   }
}