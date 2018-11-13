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

use Pho\Framework\ParticleInterface;

/**
 * Thrown when a particle is called with an unknown function
 * (e.g invalid edge etc.) 
 * 
 * Since all particle functions are dynamic (using magic method
 * _call) erroneous method calls are possible and not handled 
 * by native PHP errors.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InvalidParticleMethodException extends \Exception
{

    /**
     * Constructor.
     *
     * @param string $class_name Class name of the particle
     * @param string $method     Invalid method
     * @param string $id         ID of the particle
     */
    public function __construct(string $class_name, string $method, string $id = "unknown")
    {
        parent::__construct();
        $this->message = sprintf(
            "The particle %s with ID %s was called with the invalid method: %s",
            $class_name,
            $id,
            $method
        );
    }

}
