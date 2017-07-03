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
 * Thrown in an attempt to form a new node (with a formative edge) but the given
 * parameters are not recognizable.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class UnrecognizedSetOfParametersForFormativeEdgeException extends \Exception
{

    /**
     * Constructor.
     *
     * @param string $params              The params in string:::array etc. form.
     */
    public function __construct(string $params, array $patterns)
    {
        parent::__construct();
        $this->message = sprintf(
            "The set of parameters (%s) were not recognized. Existing patterns are: %s",
            $params,
            print_r($patterns, true)
        );
    }

}