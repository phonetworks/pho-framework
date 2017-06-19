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
 * Thrown when a particle's edge method is called with an argument of type that 
 * is not supported by the edge itself.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InvalidEdgeHeadTypeException extends \Exception
{

    /**
     * Constructor.
     *
     * @param ParticleInterface $object
     * @param array             $settables
     */
    public function __construct(ParticleInterface $object, array $settables)
    {
        parent::__construct();
        $this->message = sprintf(
            "The given edge head is of type %s. Only the following types are allowed; %s",
            get_class($object),
            implode(", ", $settables)
        );
    }

}