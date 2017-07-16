<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Cargo;

/**
 * Holds variables in regards to incoming edges of a particle
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class IncomingEdgeCargo extends AbstractCargo
{
    /**
     * Incoming Edges
     * 
     * Lists edges that are directed towards this node. Initiliazed at construction,
     * and never modified again.
     * 
     * @var array An array of class names (with their namespaces)
     */
    public $edges_in = [];


    /**
     * Getter Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges in plural.
     * Tail labels in string format.
     *
     * @var array
     */
    public $edge_in_getter_methods = [];

    /**
     * Getter Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges in plural as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    public $edge_in_getter_classes = [];

    /**
     * Haser Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges in singular.
     * Tail labels in string format.
     *
     * @var array
     */
    public $edge_in_haser_methods = [];

    /**
     * Haser Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges in singular as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    public $edge_in_haser_classes = [];
}