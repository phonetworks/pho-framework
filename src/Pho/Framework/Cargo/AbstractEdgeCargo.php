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
 * An edge cargo class holds variables in regards to particle edges.
 * 
 * This helper class' variables are set at construction of a particle,
 * then accessed by handlers.
 * 
 * The variables are public, but the class shall be kept in a protected 
 * variable within the shell class, hence, not exposed to end-users.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
abstract class AbstractEdgeCargo implements CargoInterface
{
    /**
     * @var array An array of class names (with their namespaces)
     */
    public $classes = [];

    /**
     * @var array An array of singular labels in string format.
     */
    public $singularLabels = [];

    /**
     * @var array An array of plural labels in string format.
     */
    public $labels = [];

    /**
     * @var array An array of plural labels as key, and edge class names as value.
     */
    public $label_class_pairs = [];

    /**
     * @var array An array of singular labels as key, and edge class names as value.
     */
    public $singularLabel_class_pairs = [];

}