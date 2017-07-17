<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Handlers;

use Pho\Framework\ParticleInterface;
use Pho\Framework\Exceptions\InvalidEdgeHeadTypeException;

class Set implements HandlerInterface
{

    /**
     * {@inheritDoc}
     */
    public static function handle(
        ParticleInterface $particle,
        array $pack,
        string $name, 
        array $args
        ):  \Pho\Lib\Graph\EntityInterface
    {
        $check = false;
        foreach($pack["out"]->setter_label_settable_pairs[$name] as $settable) {
            $check |= is_a($args[0], $settable);
        }
        if(!$check) { 
            throw new InvalidEdgeHeadTypeException($args[0], $pack["out"]->setter_label_settable_pairs[$name]);
        }
        $edge = new $pack["out"]->setter_classes[$name]($particle, $args[0]);
        return $edge->return();
    }
}