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
 * {@inheritDoc}
 */
abstract class AbstractOutgoingEdgeCargo extends AbstractCargo
{
    /**
     * Getter Classes of Outgoing Edges
     * 
     * Lists edges that are directed from this node. Initiliazed at construction,
     * and never modified again.
     * 
     * {@inheritDoc}
     */
    public $classes = [];


    /**
     * Getter Labels of Outgoing Edges
     * 
     * A simple array of head labels of outgoing edges in plural.
     * Tail labels in string format.
     *
     * {@inheritDoc}
     */
    public $labels = [];

    /**
     * "Getter" Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges in plural as key
     * and associated class name as value.
     * Both in string format.
     *
     * {@inheritDoc}
     */
    public $label_class_pairs = [];

    /**
     * "Has" Labels of Outgoing Edges
     * 
     * A simple array of head labels of outgoing edges in singular.
     * Labels in string format.
     *
     * {@inheritDoc}
     */
    public $singularLabels = [];

    /**
     * "Has" Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges in singular as key,
     * associated class names as value.
     * Both in string format.
     *
     * {@inheritDoc}
     */
    public $singularLabel_class_pairs = [];

}