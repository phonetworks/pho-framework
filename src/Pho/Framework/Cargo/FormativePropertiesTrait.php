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
     * @var array Edge Labels
     */ 
    public $formative_labels = [];

    /**
     * Formative Classes of Outgoing Edges
     * 
     * An array of edge labels as key
     * and associated edge class name as value.
     * Both in string format.
     *
     * @var array Edge Label => Edge Class
     */
    public $formative_label_class_pairs = [];    

    /**
     * Arguments that match with each formative edge out.
     *
     * In regular expression format.
     * 
     * @var array Edge label as key, arguments pattern as value
     */
    public $formative_patterns = [];
}