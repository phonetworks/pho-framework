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

use Pho\Framework\Cargo;
use Pho\Framework\Loaders;
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
     * Holds handler info for incoming edges
     *
     * @var Cargo\IncomingEdgeCargo
     */
    protected $cargo_in;

    /**
     * Holds handler info for outgoing edges
     *
     * @var Cargo\OutgoingEdgeCargo
     */
    protected $cargo_out;

    /**
     * An array of incoming edge classes
     *
     * @var array
     */
    protected $incoming_edges;

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
        
        $this->cargo_in = new Cargo\IncomingEdgeCargo($this->incoming_edges);
        $this->cargo_out = new Cargo\OutgoingEdgeCargo();

        Loaders\IncomingEdgeLoader::pack($this)
            ->deploy($this->cargo_in); 
        Loaders\OutgoingEdgeLoader::pack($this)
            ->deploy($this->cargo_out);
    }

    /**
     * Registers the incoming edges.
     *
     * The default ones for all nodes are:
     * * ActorOut\Read::class
     * * ActorOut\Subscribe::class
     * * ObjectOut\Publish::class
     * 
     * @param ...$classes 
     * 
     * @return void
     */
    protected function registerIncomingEdges(...$classes): void
    {
        foreach($classes as $class) {
            $this->incoming_edges[] = $class;
            $this->emit("incoming_edge.registered", [$class]);
        }
    }

    public function getRegisteredIncomingEdges(): array
    {
        return $this->incoming_edges;
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
        if(in_array($name, $this->cargo_out->setter_labels)) {
            return Handlers\Get::handle($name, $args);
        }
        else if(in_array($name, $this->cargo_out->formative_labels)) {
            return Handlers\Form::handle($name, $args);
        }
        else if(strlen($name) > 3) {
            $func_prefix = ucfirst(substr($name, 0, 3));
            $funcs = [
                "get"=>"Handlers\Get::handle", 
                "has"=>"Handlers\Has::handle"
            ];
            if (array_key_exists($func_prefix, $funcs) ) {
                try {
                    return $funcs[$func_prefix]($name, $args);
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