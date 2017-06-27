<?php

namespace Pho\Framework;

use Pho\Lib\Graph\EdgeInterface;
use Pho\Lib\Graph\SerializableTrait;

abstract class AbstractNotification implements \Serializable 
{

    use SerializableTrait;

    const MSG = "";

    /**
     * The edge
     *
     * Where this notification originates from.
     * 
     * @var \Pho\Lib\Graph\EdgeInterface
     */
    protected $edge;

    /**
     * The edge id.
     *
     * Kept in records for hydration/dehydration.
     * 
     * @var [type]
     */
    protected $edge_id;

    /**
     * Constructor.
     *
     * @param \Pho\Lib\Graph\EdgeInterface $edge
     */
    public function __construct(EdgeInterface $edge)
    {
        $this->edge = $edge;
        $this->edge_id = $edge->id();
    }

    public function __invoke() //: mixed
    {
        return $this->edge();
    }

    public function edge(): EdgeInterface
    {
        if(isset($this->edge)) 
            return $this->edge;
        else
            return $this->hydratedEdge();
    }

    protected function hydratedEdge(): EdgeInterface
    {

    }

    /**
     * Turns the notification into a string
     *
     * @return string
     */
    abstract public function __toString(): string;
    /*{
        $params = $this->params;
        array_unshift($params, $this->actor);
        return sprintf(static::MSG, ...$params);
    }*/

    /**
     * Dumps the object in an array
     *
     * Useful for serialization
     * 
     * @return array
     */
   public function toArray(): array
   {
        return array(
            "label" => $this->label(),
            "edge" => (string) $this->edge_id
        );
   }

   public function label(): string
   {
       return (new \ReflectionObject($this))->getShortName();
   }

/*
   public function serialize(): string
   {
       return serialize($this->toArray());
   }

   public function unserialize(string $data) //: mixed
   {
        $this->edge =
   }
   */
}