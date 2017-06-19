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
 * Thrown when the given predicate class does not exist.
 * 
 * This is called during serialization of edges. If the predicate does not
 * exist, this is thrown and it means some libraries are not installed.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class PredicateClassDoesNotExistException extends \Exception
{

    /**
     * Constructor.
     *
     * @param string $edge_id              The ID of the edge in pure string format
     * @param string $predicate_class_name Full class name of the predicate
     */
    public function __construct(string $edge_id, string $predicate_class_name)
    {
        parent::__construct();
        $this->message = sprintf(
            "The edge (%s) predicate \"%s\" cannot be found.",
            $edge_id,
            $predicate_class_name
        );
    }

}