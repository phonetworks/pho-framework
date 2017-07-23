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
     * Handles catch-all method calls.
     *
     * @var \Pho\Framework\Handlers\Gateway
     */
    protected $handler;

    /**
     * An array of incoming edge classes
     *
     * @var array
     */
    protected $incoming_edges = [];

    /**
     * An array of outgoing edge classes
     *
     * @var array
     */
    protected $outgoing_edges = [];

    /**
     * Constructor.
     */
    public function initializeParticle() 
    {
        $this->addEdges("incoming",
            ActorOut\Read::class, 
            ActorOut\Subscribe::class, 
            ObjectOut\Mention::class
        );

        $this->autoRegisterOutgoingEdges();

        if(method_exists($this, "onIncomingEdgeRegistration")) {
            $this->onIncomingEdgeRegistration();
        }
        
        $this->initializeHandler();
    }

    /**
     * A helper method to set up edges and fields.
     *
     * @return void
     */
    protected function initializeHandler(): void
    {
        $this->handler = new Handlers\Gateway($this); 
        Loaders\IncomingEdgeLoader::pack($this)
            ->deploy($this->handler->cargo_in); 
        Loaders\OutgoingEdgeLoader::pack($this)
            ->deploy($this->handler->cargo_out);
        Loaders\FieldsLoader::pack($this)
            ->deploy($this->handler->cargo_fields);
    }


    /**
     * Auto-registers outgoing edge classes
     *
     * Auto-registration is done by directory structure. Directories that sit 
     * in this folder, and are named after this class with 
     * "Out" suffix (such as "MyNodeOut" for a node class named "MyNode")
     * would be candidate for auto-registration.
     * 
     * Please note, this does not check if it's actually an Edge class. 
     * The check is done by the OutgoingEdgeLoader class.
     * 
     * @return void
     */
    protected function autoRegisterOutgoingEdges(): void
    {
        $self_reflector = new \ReflectionObject($this);
        if($self_reflector->isAnonymous()) {
            return;
        }

        $edge_dir = 
            dirname($self_reflector->getFileName()) . 
            DIRECTORY_SEPARATOR . 
            $self_reflector->getShortName() 
            . "Out";  
        // !!! do not replace this with __DIR__

        if(!file_exists($edge_dir)) {
            Logger::info("Edge directory %s does not exist", $edge_dir);
            return;
        }

        $locator = new \Zend\File\ClassFileLocator($edge_dir);
        foreach ($locator as $file) {
            $filename = str_replace($edge_dir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $this->addEdges("outgoing", ...$file->getClasses()); 
        }
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
    public function registerIncomingEdges(...$classes): void
    {
        $this
            ->addEdges("incoming", ...$classes)
            ->initializeHandler();
    }

    /**
     * Registers the outgoing edges.
     * 
     * @param ...$classes 
     * 
     * @return void
     */
    public function registerOutgoingEdges(...$classes): void
    {
        $this
            ->addEdges("outgoing", ...$classes)
            ->initializeHandler();
    }

    /**
     * A helper method to register edges
     *
     * @param string $direction Either incoming or outgoing
     * @param  ...$classes 
     * 
     * @return self
     */
    protected function addEdges(string $direction, ...$classes): self
    {
        if(!in_array($direction, ["incoming", "outgoing"])) {
            // log meaningless direction
            return $this;
        }
        $var = sprintf("%s_edges", $direction);
        foreach($classes as $class) {
            if(in_array($class, $this->$var))
                continue;
            $this->$var[] = $class;
            $this->emit("edge.registered", [$direction, $class]);
            $this->emit($direction."_edge.registered", [$class]);
        }
        return $this;
    }

    public function getRegisteredIncomingEdges(): array
    {
        return $this->incoming_edges;
    }

    public function getRegisteredOutgoingEdges(): array
    {
        return $this->outgoing_edges;
    }

    /**
     * Registers a new handler adapter.
     *
     * Default handlers may be overriden.
     * 
     * @param string $key Adapter key; e.g. "get", "set", "form" etc.
     * @param string $class Handler class to register. A handler class shall implement HandlerInterface
     * 
     * @return void
     */
    public function registerHandler(string $key, string $class): void
    {
        $this->handler->register($key, $class);
    }

    /**
     * @internal
     *
     * @param string $name
     * @param array  $args
     * @return void
     * 
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     * @throws \InvalidArgumentException $e thrown when there argument does not meet the constraints.
     */
    public function __call(string $name, array $args) 
    {
        return $this->handler->switch($name, $args);
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
        return $this->hookable();
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