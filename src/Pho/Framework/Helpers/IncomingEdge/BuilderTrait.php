<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Helpers\IncomingEdge;

/**
 * Helps set up the incoming edges of a particle (aka node)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait BuilderTrait 
{

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
     * Sets up incoming edges.
     * 
     * Given the configurations set in the particle class itself 
     * (e.g. EDGES_IN constant), configures the way the 
     * class will act.
     *
     * @return void
     */
    protected function buildIncomingEdges(): void 
    {
        //eval(\Psy\sh());
        foreach($this->edges_in as $edge_in_class) {
            $edge_in_class_reflector = new \ReflectionClass($edge_in_class);
            $check = false;
            foreach($edge_in_class_reflector->getConstant("SETTABLES") as $head_node_type) {
                $check |= is_a($this, $head_node_type);
            }
            if($edge_in_class_reflector->getConstant("SETTABLES_EXTRA")!==false) {
                foreach($edge_in_class_reflector->getConstant("SETTABLES_EXTRA") as $head_node_type) {
                    $check |= is_a($this, $head_node_type);
                }
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
            $this->emit("incoming_edge.registered", [$class]);
        }
    }
}