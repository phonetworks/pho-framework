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
use Pho\Lib\Graph\ID;

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
     * Incoming Edges
     * 
     * Lists edges that are directed towards this node. Initiliazed at construction,
     * and never modified again.
     * 
     * @var array An array of class names (with their namespaces)
     */
    protected $edges_in = [];
    
    /**
     * Getter Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges in plural.
     * Tail labels in string format.
     *
     * @var array
     */
    protected $edge_in_getter_methods = [];

    /**
     * Getter Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges in plural as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_in_getter_classes = [];

    /**
     * Haser Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges in singular.
     * Tail labels in string format.
     *
     * @var array
     */
    protected $edge_in_haser_methods = [];

    /**
     * Haser Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges in singular as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_in_haser_classes = [];

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
     * A simple array of head labels of outgoing edges in plural.
     * Labels in string format.
     *
     * @var array
     */
    protected $edge_out_getter_methods = [];

    /**
     * Getter Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges in plural as key,
     * associated class names as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_getter_classes = [];

    /**
     * Haser Labels of Outgoing Edges
     * 
     * A simple array of head labels of outgoing edges in singular.
     * Labels in string format.
     *
     * @var array
     */
    protected $edge_out_haser_methods = [];

    /**
     * Haser Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges in singular as key,
     * associated class names as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_haser_classes = [];

    /**
     * Access Control List object
     * 
     * Null since it is not implemented at this level.
     *
     * @var null
     */
    // protected $acl = null;

    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->registerIncomingEdges(
            ActorOut\Read::class, 
            ActorOut\Subscribe::class, 
            ObjectOut\Transmit::class
        );
        $this->_setupEdgesIn();
        $this->_setupEdgesOut();
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
            $this->edges_in[] = $class;
        }
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
        foreach($this->edges_in as $edge_in_class) {
            $edge_in_class_reflector = new \ReflectionClass($edge_in_class);
            $check = false;
            foreach($edge_in_class_reflector->getConstant("SETTABLES") as $head_node_type) {
                $check |= is_a($this, $head_node_type);
            }
            if($check) {
                $method = $edge_in_class_reflector->getConstant("TAIL_LABELS");
                $this->edge_in_getter_methods[] = $method;
                $this->edge_in_getter_classes[$method] = $edge_in_class;
                $method = $edge_in_class_reflector->getConstant("TAIL_LABEL");
                $this->edge_in_haser_methods[] = $method;
                $this->edge_in_haser_classes[$method] = $edge_in_class;
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

        if(!file_exists($edge_dir)) {
            Logger::warning("Edge directory %s does not exist", $edge_dir);
            return;
        }

        $locator = new ClassFileLocator($edge_dir);
        foreach ($locator as $file) {
            $filename = str_replace($edge_dir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            foreach ($file->getClasses() as $class) {
                $reflector = new \ReflectionClass($class);
                if(!$reflector->isSubclassOf(AbstractEdge::class)) { 
                    continue 1;
                }
                $_method = (string) strtolower($reflector->getShortName());
                $this->edge_out_setter_methods[] = $_method;
                $this->edge_out_setter_classes[$_method] = $class;
                $this->edge_out_setter_settables[$_method] = $reflector->getConstant("SETTABLES");
                $_method = $reflector->getConstant("HEAD_LABELS");
                $this->edge_out_getter_methods[] = $_method;
                $this->edge_out_getter_classes[$_method] = $class;
                $_method = $reflector->getConstant("HEAD_LABEL");
                $this->edge_out_haser_methods[] = $_method;
                $this->edge_out_haser_classes[$_method] = $class;

            }
        }
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
     * Catch-all method for setters
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return \Pho\Lib\Graph\EntityInterface Returns \Pho\Lib\Graph\EdgeInterface by default, but in order to provide flexibility for higher-level components to return node (in need) the official return value is \Pho\Lib\Graph\EntityInterface which is the parent of both NodeInterface and EdgeInterface.
     */
    protected function _callSetter(string $name, array $args):  \Pho\Lib\Graph\EntityInterface
    {
        $check = false;
        foreach($this->edge_out_setter_settables[$name] as $settable) {
            $check |= is_a($args[0], $settable);
        }
        if(!$check) { 
            throw new InvalidEdgeHeadTypeException($args[0], $this->edge_out_setter_settables[$name]);
        }
        $edge = new $this->edge_out_setter_classes[$name]($this, $args[0]);
        return $edge->return();
    }

    /**
     * Catch-all method for getters
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return array An array of ParticleInterface objects
     * 
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    protected function _callGetter(string $name, array $args): array
    {
        $name = strtolower(substr($name, 3));
        if(in_array($name, $this->edge_out_getter_methods)) {
            return $this->__callGetterEdgeOut($name);
        }   
        else if(in_array($name, $this->edge_in_getter_methods)) {
            return $this->__callGetterEdgeIn($name);
        }
        throw new Exceptions\InvalidParticleMethodException(__CLASS__, $name);
    }


    /**
     * Getter Catcher for Edges Out
     *
     * @param string $name Representation of nodes to retrieve
     * 
     * @return array The edges.
     */
    protected function __callGetterEdgeOut(string $name): array
    {
        $edges_out = $this->edges()->out();
        $return = [];
        array_walk(
            $edges_out, function ($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_out_getter_classes[$name]) {
                    $return[] = $item();
                }
            }
        );
        return $return;
    }

    /**
     * Getter Catcher for Edges In
     *
     * @param string $name Representation of nodes to retrieve
     * 
     * @return array The edges.
     */
    protected function __callGetterEdgeIn(string $name): array
    {
        $edges_in = $this->edges()->in();
        $return = [];
        array_walk(
            $edges_in, function ($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_in_getter_classes[$name]) {
                    $return[] = $item->tail()->node();
                }
            }
        );
        return $return;
    }


    /**
     * Catch-all method for hasers -hasSomething()-
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments. Must contain a single ID for the queried object, or it will throw an exception.
     * 
     * @return bool whether the node exists or not
     * 
     * @throws InvalidArgumentException when the method is called without a single ID object as argument.
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    protected function _callHaser(string $name, array $args): bool
    {
        if(!isset($args[0]) || !$args[0] instanceof ID ) {
            throw new \InvalidArgumentException(
                sprintf('The function %s must be called with a single argument that is strictly a \Pho\Lib\Graph\ID object', $name)
            );
        }
        $id = $args[0];
        $original_name = $name;
        $name = strtolower(substr($name, 3));
        if(in_array($name, $this->edge_out_haser_methods)) {
            return $this->__callHaserEdgeOut($id, $name);
        }   
        else if(in_array($name, $this->edge_in_haser_methods)) {
            return $this->__callHaserEdgeIn($id, $name);
        }
        throw new Exceptions\InvalidParticleMethodException(__CLASS__, $original_name);
    }


    /**
     * Haser Catcher for Edges Out
     *
     * @param string $name Representation of nodes to check
     * 
     * @return bool whether the node exists or not
     */
    protected function __callHaserEdgeOut(ID $id, string $name): bool
    {
        $edges_out = $this->edges()->out();
        foreach($edges_out as $edge) {
            if($edge instanceof $this->edge_out_haser_classes[$name] && $edge->headID()->equals($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Haser Catcher for Edges In
     *
     * @param string $name Representation of nodes to check
     * 
     * @return bool whether the node exists or not
     */
    protected function __callHaserEdgeIn(ID $id, string $name): bool
    {
        $edges_in = $this->edges()->in();
        foreach($edges_in as $edge) {
            if($edge instanceof $this->edge_in_haser_classes[$name] && $edge->tailID()->equals($id)) {
                return true;
            }
        }
        return false;
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
        if(isset($this->creator)) {
            return $this->creator;
        } else {
            return $this->hydratedCreator();
        }
    }

    /**
     * A protected hydrating method for persistence
     *
     * @see creator() to see where this is called from.
     * 
     * @return Actor
     */
    protected function hydratedCreator(): Actor
    {

    }


}