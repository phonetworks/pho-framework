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
use Pho\Lib\Graph\ID;
use Pho\Lib\Graph\Direction;
use Pho\Framework\Cargo\FieldsCargo;


/**
 * Getter Handler
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Get implements HandlerInterface
{

    /**
     * Direction out: is equivalent to head nodes of an edge
     * Direction in: is equivalent to tail nodes of an edge
     * 
     * @var array
     */
    const ADJACENCY_EQUIVALENT = [
        "out" => "head",
        "in" => "tail"
    ];

    /**
     * {@inheritDoc}
     */
    public static function handle(
        ParticleInterface $particle,
        array $pack,
        string $name, 
        array $args // no use -yet-
        ) /*:  array*/
    {
        $name =  lcfirst(substr($name, 3)); // we don't camelize because we want the method to come in proper format.
        if(static::methodExists($pack, $name, Direction::out())) {
            return static::getEdgeNodes($particle, $pack, $name, Direction::out());
        }   
        elseif(static::methodExists($pack, $name, Direction::in())) {
            return static::getEdgeNodes($particle, $pack, $name, Direction::in());
        }
        elseif( Utils::fieldExists($pack["fields"], ($name=ucfirst($name))) ) {
            return static::getField($particle, $pack["fields"], $name, $args);
        }
        throw new \Pho\Framework\Exceptions\InvalidParticleMethodException(__CLASS__, $name);
    }

    /**
     * Returns the field value
     *
     * @param ParticleInterface $particle
     * @param FieldsCargo $cargo
     * @param string $name Field name
     * @param array $args Any arguments if available
     * 
     * @return mixed Field value
     */
    protected static function getField(
        ParticleInterface $particle,
        FieldsCargo $cargo,
        string $name,
        array $args = []
    )/*: mixed*/
    {
        $name = Utils::findFieldName($cargo, $name);
        return $particle->attributes()->$name; // test with null.
    }

    /**
     * Whether the given method is available for incoming or outgoing edges.
     *
     * @param array $pack Holds incoming and outgoing cargos.
     * @param string $name Method name. Queried among incoming and outgoing labels.
     * @param Direction $direction Direction in question; in or out.
     * 
     * @return bool
     */
    protected static function methodExists(
        array $pack,
        string $name,
        Direction $direction 
        ): bool
    {
        return in_array($name, $pack[(string) $direction]->labels);
    }


    /**
     * Getter Catcher for Edges In and Out
     *
     * @param ParticleInterface $particle The particle that this handler is associated with.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name The edge node label in plural.
     * @param Direction  $direction The direction of the edge adjacent nodes to look up.
     * 
     * @return array The edge nodes.
     */
    protected static function getEdgeNodes(
        ParticleInterface $particle, 
        array $pack,
        string $name,
        Direction $direction 
        ): array
    {
        // $name = strtolower($name); // this is now taken care of beforehand.
        $direction = (string) $direction;
        $node_adj = static::ADJACENCY_EQUIVALENT[$direction];
        $cargo = $pack[$direction];
        $edges = $particle->edges()->$direction();
        $return = [];
        array_walk(
            $edges, function ($item, $key) use (&$return, $name, $cargo, $node_adj) {
                if($item instanceof $cargo->label_class_pairs[$name]) {
                    $return[] = $item->$node_adj()->node();
                }
            }
        );
        return $return;
    }

}
