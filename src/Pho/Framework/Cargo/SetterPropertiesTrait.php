<?php

namespace Pho\Framework\Cargo;

/**
 * Setter properties of outgoing edges.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait SetterPropertiesTrait
{
    /**
     * Setter Labels of Outgoing Edges
     * 
     * A simple array of edge names
     *
     * @var array
     */
    protected $setter_labels = [];

    /**
     * Setter Classes of Outgoing Edges
     * 
     * An array of edge labels as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $setter_classes = [];

    /**
     * Class Associations for Outgoing Edges 
     * 
     * An array of particle types that can be set by this
     * particle's outgoing edges. Edge labels (string) as 
     * key, settables as array.
     *
     * @var array
     */
    protected $setter_label_settable_pairs = [];
    
}