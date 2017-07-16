<?php

namespace Pho\Framework\Helpers\OutgoingEdge;

trait SetterPropertiesTrait
{
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
}