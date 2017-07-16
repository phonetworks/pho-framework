<?php

namespace Pho\Framework\Helpers\OutgoingEdge;

trait FormativePropertiesTrait
{
    /**
     * Formative Labels of Outgoing Edges
     * 
     * A simple array of edge names
     * 
     * @var array
     */ 
    protected $edge_out_formative_methods = [];

    /**
     * Formative Classes of Outgoing Edges
     * 
     * An array of edge labels as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_formative_edge_classes = [];    

    /**
     * Arguments that match with each formative edge out.
     *
     * In regular expression format.
     * 
     * @var array
     */
    protected $edge_out_formative_edge_patterns = [];
}