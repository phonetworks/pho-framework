<?php

namespace Pho\Framework\Exceptions;

use Pho\Framework\NodeInterface;

/**
 * Thrown when a node's edge method is called with an argument of type that 
 * is not supported by the edge itself.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InvalidEdgeHeadTypeException extends \Exception {

    /**
     * Constructor.
     *
     * @param NodeInterface $object
     * @param array $settables
     */
    public function __construct(NodeInterface $object, array $settables)
    {
        $this->message = sprintf(
            "The given edge head is of type %s. Only the following types are allowed; %s",
            get_class($object),
            implode(", ", $settables)
        );
    }

}