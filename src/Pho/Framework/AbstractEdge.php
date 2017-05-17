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
abstract class AbstractEdge extends \Pho\Lib\Graph\Edge {

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
     * When invoked, returns the head node.
     *
     * @return ParticleInterface
     */
    public function __invoke(): ParticleInterface
    {
        return $this->head()->node();
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
       if(class_exists($data["predicate"]))
        $this->predicate_label = new $data["predicate"];
        else
        throw new PredicateClassDoesNotExistException((string)$this->id(), $data["predicate"])
       $this->attributes = new Graph\AttributeBag($this, $data["attributes"]);
    }

}