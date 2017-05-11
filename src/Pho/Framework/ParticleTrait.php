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

use Pho\Framework\Exceptions\InvalidEdgeHeadTypeException;
use Zend\File\ClassFileLocator;

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
trait ParticleTrait {

    /**
     * Who created this node. Must point to an Actor.
     * 
     * Points to self by Actor particles.
     *
     * @var Actor
     */
    protected $creator;

    /**
     * @internal 
     * 
     * Incoming Edges
     * 
     * A constant node property of edges that are directed towards this node.
     * 
     * @var array An array of class names (with their namespaces)
     */
    // const EDGES_IN = [];
    
    /**
     * Getter Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges.
     * Tail labels in string format.
     *
     * @var array
     */
    protected $edge_in_getter_methods = [];

    /**
     * Getter Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_in_getter_classes = [];

    /**
     * Setter Labels of Outgoing Edges
     * 
     * A simple array of edge names
     *
     * @var array
     */
    protected $edge_out_setter_methods = [];

    /**
     * Setter Classes of Outgoing Edges
     * 
     * An array of edge labels as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_setter_classes = [];

    /**
     * Class Associations for Outgoing Edges 
     * 
     * An array of particle types that can be set by this
     * particle's outgoing edges. Edge labels (string) as 
     * key, settables as array.
     *
     * @var array
     */
    protected $edge_out_setter_settables = [];

    /**
     * Getter Labels of Outgoing Edges
     * 
     * A simple array of head labels of outgoing edges.
     * Labels in string format.
     *
     * @var array
     */
    protected $edge_out_getter_methods = [];

    /**
     * Getter Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges as key,
     * associated class names as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_getter_classes = [];

    /**
     * Access Control List object
     * 
     * Null since it is not implemented at this level.
     *
     * @var null
     */
    // protected $acl = null;

    /**
     * Trait constructor.
     */
    protected function setupEdges() {
        $this->_setupEdgesIn();
        $this->_setupEdgesOut();
    }

    /**
     * Sets up incoming edges.
     * 
     * Given the configurations set in the particle class itself 
     * (e.g. EDGES_IN constant), configures the way the 
     * class will act.
     *
     * @return void
     */
    protected function _setupEdgesIn(): void 
    {
        //eval(\Psy\sh());
        foreach(static::EDGES_IN as $edge_in_class) {
            $edge_in_class_reflector = new \ReflectionClass($edge_in_class);
            $check = false;
            foreach($edge_in_class_reflector->getConstant("SETTABLES") as $head_node_type)
                $check |= is_a($this, $head_node_type);
            if($check) {
                $method = $edge_in_class_reflector->getConstant("TAIL_LABELS");
                $this->edge_in_getter_methods[] = $method;
                $this->edge_in_getter_classes[$method] = $edge_in_class;
            }
        }
    }

    /**
     * Sets up outgoing edges.
     * 
     * Given the configurations set in {ClassName}/{EdgeName}
     * classes , configures the way the class will act.
     *
     * @return void
     */
    protected function _setupEdgesOut(): void
    {
        // !!! we use reflection method so that __DIR__ behaves properly with child classes.
        $self_reflector = new \ReflectionObject($this);
        $edge_dir = dirname($self_reflector->getFileName()) . DIRECTORY_SEPARATOR . $self_reflector->getShortName() . "Out";  
        // !!! do not replace this with __DIR__

        if(!file_exists($edge_dir))
            return;

        $locator = new ClassFileLocator($edge_dir);
        foreach ($locator as $file) {
            $filename = str_replace($edge_dir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            foreach ($file->getClasses() as $class) {
                $reflector = new \ReflectionClass($class);
                if(!$reflector->isSubclassOf(AbstractEdge::class)) 
                    continue 1;
                $_method = (string) strtolower($reflector->getShortName());
                $this->edge_out_setter_methods[] = $_method;
                $this->edge_out_setter_classes[$_method] = $class;
                $this->edge_out_setter_settables[$_method] = $reflector->getConstant("SETTABLES");
                $_method = $reflector->getConstant("HEAD_LABELS");
                $this->edge_out_getter_methods[] = $_method;
                $this->edge_out_getter_classes[$_method] = $class;
            }
        }
    }

    /**
     * @internal
     *
     * @param string $name
     * @param array $args
     * @return void
     * 
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    public function __call(string $name, array $args) {
        if(in_array($name, $this->edge_out_setter_methods)) {
            return $this->_callSetter($name, $args);
        }
        else if( (strlen($name) > 3 && substr($name, 0, 3) == "get" ) ) {
            try {
                return $this->_callGetter($name, $args);
            }
            catch(InvalidParticleMethodException $e) {
                throw $e;
            }
        }
        throw new Exceptions\InvalidParticleMethodException(__CLASS__, $name);
    }

    /**
     * Catch-all method for setters
     *
     * @param string $name Catch-all method name
     * @param array $args Catch-all method arguments
     * @return \Pho\Lib\Graph\NodeInterface
     */
    protected function _callSetter(string $name, array $args): AbstractEdge
    {
        $check = false;
        foreach($this->edge_out_setter_settables[$name] as $settable)
            $check |= is_a($args[0], $settable);
        if(!$check) 
            throw new InvalidEdgeHeadTypeException($args[0], $this->edge_out_setter_settables[$name]);
        $edge = new $this->edge_out_setter_classes[$name]($this, $args[0]);
        return $edge;
        // return $edge(); // returns the head() // not at framework level.
    }

    /**
     * Catch-all method for getters
     *
     * @param string $name Catch-all method name
     * @param array $args Catch-all method arguments
     * 
     * @return array An array of ParticleInterface objects
     * 
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    protected function _callGetter(string $name, array $args): array
    {
        $name = strtolower(substr($name, 3));
        if(in_array($name, $this->edge_out_getter_methods)) {
            $edges_out = $this->edges()->out();
            $return = [];
            array_walk($edges_out, function($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_out_getter_classes[$name])
                   $return[] = $item();
            });
            return $return;
        }   
        else if(in_array($name, $this->edge_in_getter_methods)) {
            $edges_in = $this->edges()->in();
            $return = [];
            array_walk($edges_in, function($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_in_getter_classes[$name])
                   $return[] = $item->tail()->node();
            });
            return $return;
        }
        throw new Exceptions\InvalidParticleMethodException(__CLASS__, $name);
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
        $array["creator"] = $this->creator->id();
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function existentials(): array
    {
        return [
            "node"    => $this,
            "creator" => $this->creator,
            "context" => $this->context
        ];
    }


}