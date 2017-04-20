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
use Pho\Framework\Exceptions\InvalidEdgeHeadTypeException;
use Zend\File\ClassFileLocator;

abstract class AbstractNode extends Graph\Node implements NodeInterface {

    /**
     * A constant node property of edges that are directed towards this node.
     * 
     * @var array An array of class names (with their namespaces)
     */
    const EDGES_IN = [];
    
    /**
     * A simple array of tail labels of incoming edges.
     * Tail labels in string format.
     *
     * @var array
     */
    protected $edge_in_getter_methods = [];

    /**
     * An array of tail labels of incoming edges as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_in_getter_classes = [];

    /**
     * A simple array of edge names
     *
     * @var array
     */
    protected $edge_out_setter_methods = [];

    /**
     * An array of edge labels as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_setter_classes = [];

    /**
     * An array of node types that can be set by this
     * node's outgoing edges. Edge labels (string) as 
     * key, settables as array.
     *
     * @var array
     */
    protected $edge_out_setter_settables = [];

    /**
     * A simple array of head labels of outgoing edges.
     * Labels in string format.
     *
     * @var array
     */
    protected $edge_out_getter_methods = [];

    /**
     * An array of head labels of outgoing edges as key,
     * associated class names as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_getter_classes = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(Graph\GraphInterface $graph) {
        parent::__construct($graph);
        $this->_setupEdgesIn();
        $this->_setupEdgesOut();
    }

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

    protected function _setupEdgesOut(): void
    {
        // we use reflection method so that __DIR__ behaves properly with child classes.
        $self_reflector = new \ReflectionObject($this);
        $edge_dir = dirname($self_reflector->getFileName()) . DIRECTORY_SEPARATOR . $self_reflector->getShortName();  
        //

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
    

    

    public function __call(string $name, array $args) {
        if(in_array($name, $this->edge_out_setter_methods)) {
            $check = false;
            foreach($this->edge_out_setter_settables[$name] as $settable)
                $check |= is_a($args[0], $settable);
            if(!$check) 
                throw new InvalidEdgeHeadTypeException($args[0], $this->edge_out_setter_settables[$name]);
            return new $this->edge_out_setter_classes[$name]($this, $args[0]);
        }
        else if( ! (strlen($name) > 3 && substr($name, 0, 3) == "get" ) ) {
            return;
        }
        $name = strtolower(substr($name, 3));
        if(in_array($name, $this->edge_out_getter_methods)) {
            $edges_out = $this->edges()->out();
            $return = [];
            array_walk($edges_out, function($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_out_getter_classes[$name])
                   $return[] = $item;
            });
            return $return;
        }   
        else if(in_array($name, $this->edge_in_getter_methods)) {
            $edges_in = $this->edges()->in();
            $return = [];
            array_walk($edges_in, function($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_in_getter_classes[$name])
                   $return[] = $item;
            });
            return $return;
        }
    }


}