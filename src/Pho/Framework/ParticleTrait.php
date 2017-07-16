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

use Pho\Framework\Exceptions\UnrecognizedSetOfParametersForFormativeEdgeException;

/**
 * The Particle Trait
 * 
 * This constitutes the basis of all particle classes that are part of the
 * Pho Framework; namely {@link Actor},  {@link Frame} and {@link Object}.
 * 
 * Pho Framework particles extend Pho\Lib\Graph\Node
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait ParticleTrait
{

    use Helpers\IncomingEdge\BuilderTrait;
    use Helpers\OutgoingEdge\BuilderTrait;
    use Handlers\SetterHandlerTrait;
    use Handlers\GetterHandlerTrait;

    /**
     * Who created this node. Must point to an Actor.
     * 
     * Points to self by Actor particles.
     *
     * @var Actor
     */
    protected $creator;


    /**
     * The creator's ID
     *
     * @var string
     */
    protected $creator_id;

    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->registerIncomingEdges(
            ActorOut\Read::class, 
            ActorOut\Subscribe::class, 
            ObjectOut\Mention::class
        );
        $this->onConstruction();
        $this->buildIncomingEdges();
        $this->buildOutgoingEdges($this);
    }

    /**
     * @internal
     *
     * @param string $name
     * @param array  $args
     * @return void
     * 
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    public function __call(string $name, array $args) 
    {
        if(in_array($name, $this->edge_out_setter_methods)) {
            return $this->_callSetter($name, $args);
        }
        else if(in_array($name, $this->edge_out_formative_methods)) {
            return $this->_callFormer($name, $args);
        }
        else if(strlen($name) > 3) {
            $func_prefix = substr($name, 0, 3);
            $funcs = ["get"=>"_callGetter", "has"=>"_callHaser"];
            if (array_key_exists($func_prefix, $funcs) ) {
                try {
                    return $this->{$funcs[$func_prefix]}($name, $args);
                }
                catch(Exceptions\InvalidParticleMethodException $e) {
                    throw $e;
                }
            }
        }
        //throw new Exceptions\InvalidParticleMethodException(__CLASS__, $name);
    }

    /**
     * Converts the particle into array
     * 
     * For serialization and portability.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array["creator"] = $this->creator_id;
        if($this instanceof Actor) {
            $array["notifications"] = $this->notifications()->toArray();
        }
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function existentials(): array
    {
        return [
            "node"    => $this,
            "creator" => $this->creator(),
            "context" => $this->context()
        ];
    }

    /**
     * Retrieves the creator of this node.
     *
     * @return Actor
     */
    public function creator(): Actor
    {
        if(isset($this->creator))
            return $this->creator;
        return $this->hyCreator();
    }

    /**
     * A protected hydrating method for persistence
     *
     * @see creator() to see where this is called from.
     * 
     * @return Actor
     */
    protected function hyCreator(): Actor
    {

    }


    /**************************************************
     * The rest are Subscription/Publisher related 
     * functions
     *************************************************/

    /**
     * Notifies observers about deletion
     * 
     * @return void
     */
    public function notifySubscribers(AbstractNotification $notification): void
    {
        foreach ($this->getSubscribers() as $subscriber) {
            $subscriber->notifications()->add($notification);
        }
    }

}