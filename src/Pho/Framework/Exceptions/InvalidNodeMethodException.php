<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Exceptions;

use Pho\Framework\NodeInterface;

/**
 * Thrown when a node is called with an unknown function
 * (e.g invalid edge etc.) 
 * 
 * Since all node functions are dynamic (using magic method
 * _call) erroneous method calls are possible and not handled 
 * by native PHP errors.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InvalidNodeMethodException extends \Exception {

    /**
     * Constructor.
     *
     * @param NodeInterface $object
     * @param array $settables
     */
    public function __construct(string $class_name, string $method)
    {
        parent::__construct();
        $this->message = sprintf(
            "The node %s was called with the invalid method: %s",
            $class_name,
            $method
        );
    }

}