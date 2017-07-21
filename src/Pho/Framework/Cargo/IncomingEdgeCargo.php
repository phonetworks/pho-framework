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
class IncomingEdgeCargo extends AbstractEdgeCargo
{
    /**
     * Incoming Edges
     * 
     * Lists edges that are directed towards this node. Initiliazed at construction,
     * and never modified again.
     * 
     * {@inheritDoc}
     */
    public $classes = [];


    /**
     * Getter Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges in plural.
     * Tail labels in string format.
     *
     * {@inheritDoc}
     */
    public $labels = [];

    /**
     * Getter Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges in plural as key
     * and associated class name as value.
     * Both in string format.
     *
     * {@inheritDoc}
     */
    public $label_class_pairs = [];

    /**
     * "Has" Labels of Incoming Edges
     * 
     * A simple array of tail labels of incoming edges in singular.
     * Tail labels in string format.
     *
     * {@inheritDoc}
     */
    public $singularLabels = [];

    /**
     * "Has" Classes of Incoming Edges
     * 
     * An array of tail labels of incoming edges in singular as key
     * and associated class name as value.
     * Both in string format.
     *
     * {@inheritDoc}
     */
    public $singularLabel_class_pairs = [];

}