<?php

namespace Pho\Framework\Cargo;

/**
 * Formative properties of outgoing edges.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait FormativePropertiesTrait
{
    /**
     * Formative Labels of Outgoing Edges
     * 
     * A simple array of edge names
     * 
     * @var array
     */ 
    protected $formative_labels = [];

    /**
     * Formative Classes of Outgoing Edges
     * 
     * An array of edge labels as key
     * and associated class name as value.
     * Both in string format.
     *
     * @var array
     */
    protected $formative_label_class_pairs = [];    

    /**
     * Arguments that match with each formative edge out.
     *
     * In regular expression format.
     * 
     * @var array
     */
    protected $formative_patterns = [];
}