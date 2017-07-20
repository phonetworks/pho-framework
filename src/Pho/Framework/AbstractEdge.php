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

use Pho\Lib\Graph;

/**
 * Framework Edge Foundation
 *
 * This abstract class extends {@link \Pho\Lib\Graph\Edge}
 * and acts as a placeholder that defines that its subclasses
 * must implement HEAD_LABELS, TAIL_LABEL,TAIL_LABELS
 * and SETTABLES constants.
 *
 * @author Emre Sokullu <emre@phonetworks.org>
 */
abstract class AbstractEdge 
    extends \Pho\Lib\Graph\Edge 
    implements InjectableInterface
{

    use InjectableTrait;

    /**
     * Head Node Label in Singular Form
     *
     * This is what the head node will be called.
     * For example; for {@link ActorOut/Subscribes}
     * edge, it will be "Subscription".
     */
    const HEAD_LABEL = "";

    /**
     * Head Node Label in Plural Form
     *
     * Same as above, except written in plural
     * form.
     */
    const HEAD_LABELS = "";

    /**
     * Tail Node Label in Singular Form
     *
     * This is what the tail node will be called.
     * For example; for {@link ActorOut/Subscribes}
     * edge, it will be "Subscriber".
     */
    const TAIL_LABEL = "";

    /**
     * Tail Node Label in Plural Form
     *
     * Same as above, except written in plural
     * form.
     */
    const TAIL_LABELS = "";

    /**
     * The classes this edge can be directed towards.
     */
    const SETTABLES = [];

    /**
     * The notification object associated with this edge.
     *
     * Optional. Not always formed.
     * 
     * @var AbstractNotification
     */
    protected $notification;

    public function __construct(ParticleInterface $tail, ?ParticleInterface $head = null, ?Predicate $predicate = null) 
    {
        parent::__construct(
            $tail, 
            $head, 
            $this->resolvePredicate($predicate, Predicate::class)
        );
        $this->_setNotification()->execute();
    }

    protected function _setNotification(): AbstractEdge
    {
        $is_a_notification = function(string $class_name): bool
        {
            if(!class_exists($class_name))
                return false;
            $reflector = new \ReflectionClass($class_name);
            return $reflector->isSubclassOf(AbstractNotification::class);
        };

            $notification_class = get_class($this)."Notification";
            if($is_a_notification($notification_class)) {
                $this->notification = new $notification_class($this);
            }
        
        return $this;
    }

    protected function execute(): void
    {

    }

    /**
     * When invoked, returns the head node.
     *
     * @return ParticleInterface
     */
    public function __invoke(): ParticleInterface
    {
        if($this->head() instanceof ParticleInterface) { 
            return $this->head();
        } else {
            return $this->head()->node();
        }
    }
    
    
    /**
     * @internal
     *
     * Used for serialization. Nothing special here. Declared for
     * subclasses.
     *
     * @return string in PHP serialized format.
     */
    public function serialize(): string
    {
        return serialize($this->toArray());
    }
    
    
    /**
     * @internal
     *
     * Used for deserialization. Nothing special here. Declared for
     * subclasses.
     *
     * @param string $data
     *
     * @return void
     *
     * @throws Exceptions\PredicateClassDoesNotExistException when the predicate class does not exist.
     */
    public function unserialize(/* mixed */ $data): void
    {
        $data = unserialize($data);
        $this->id = Graph\ID::fromString($data["id"]);
        $this->tail_id = $data["tail"];
        $this->head_id = $data["head"];
        if (class_exists($data["predicate"])) {
            $this->predicate_label = new $data["predicate"];
        } else {
            throw new Exceptions\PredicateClassDoesNotExistException((string)$this->id(), $data["predicate"]);
        }
        $this->attributes = new Graph\AttributeBag($this, $data["attributes"]);
    }

    /**
     * Returns the edge's value
     * 
     * If its predicate is consumer, then the head node, otherwise
     * the edge itself.
     *
     * @return \Pho\Lib\Graph\EntityInterface
     */
    public function return(): \Pho\Lib\Graph\EntityInterface
    {
        if($this->predicate()->consumer()) {
            return $this->head()->node();
        }
        return $this;
    }
}
