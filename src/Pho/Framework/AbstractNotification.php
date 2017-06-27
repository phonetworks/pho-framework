<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework;

use Pho\Lib\Graph\EdgeInterface;
use Pho\Lib\Graph\SerializableTrait;

/**
 * An abstract Notification class.
 * 
 * Notifications are the messages passed between notifiers and objects, or 
 * subscribers and their subscriptions. Notifications constitute a basic component
 * of all social networks.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
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

    /**
     * A shortcut to the edge() method.
     *
     * @see edge
     * 
     * @return mixed It's actually the EdgeInterface method.
     */
    public function __invoke() //: mixed
    {
        return $this->edge();
    }

    /**
     * Returns the edge of this notification.
     *
     * @return EdgeInterface The edge associated with this notification.
     */
    public function edge(): EdgeInterface
    {
        if(isset($this->edge)) 
            return $this->edge;
        else
            return $this->hydratedEdge();
    }

    /**
     * Used to retrieve edge after serialization/deserialization.
     * 
     * As of this layer, this method is not implemented, but declared to 
     * be implemented by higher levels that may require persistence 
     * features.
     *
     * @return EdgeInterface
     */
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

   /**
    * The class name of this notification.
    *
    * Important, since the class name is what defines the
    * notification message and behaviour.
    *
    * @return string
    */
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