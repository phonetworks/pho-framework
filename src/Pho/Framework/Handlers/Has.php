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

/**
 * "Has" Handler
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Has implements HandlerInterface
{

    /**
     * Direction out: is equivalent to head nodes of an edge
     * Direction in: is equivalent to tail nodes of an edge
     * 
     * @var array
     */
    const ADJACENCY_EQUIVALENT = [
        "out" => "headID",
        "in" => "tailID"
    ];

    /**
     * Catch-all method for hasers -hasSomething()-
     *
     * {@inheritDoc}
     */
    public static function handle(
        ParticleInterface $particle,
        array $pack,
        string $name, 
        array $args
        ) /*:  bool*/
    {
        if(!isset($args[0]) || !$args[0] instanceof ID) {
            throw new \InvalidArgumentException(
                sprintf('The function %s must be called with a single argument that is strictly a \Pho\Lib\Graph\ID object', $name)
            );
        }
        $id = $args[0];
        $original_name = $name;
        $name = lcfirst(substr($name, 3)); // we don't camelize, because we want the call to be made in proper format.


        if(static::edgeMethodExists($pack, $name, $id, Direction::out())) {
            return static::checkEdgeNode($particle, $pack, $name, $id, Direction::out());
        }   
        elseif(static::edgeMethodExists($pack, $name, $id, Direction::in())) {
            return static::checkEdgeNode($particle, $pack, $name, $id, Direction::in());
        }
        elseif(static::edgeCallableExists($pack, $name, $id, Direction::out())) {
            return static::checkEdgeItself($particle, $pack, $name, $id, Direction::out());
        }   
        elseif(static::edgeCallableExists($pack, $name, $id, Direction::in())) {
            return static::checkEdgeItself($particle, $pack, $name, $id, Direction::in());
        }

        throw new \Pho\Framework\Exceptions\InvalidParticleMethodException(__CLASS__, $original_name);
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
    protected static function edgeMethodExists(
        array $pack,
        string $name,
        ID $id,
        Direction $direction 
        ): bool
    {
        return in_array($name, $pack[(string) $direction]->singularLabels);
    }


    /**
     * Whether the given method is available for incoming or outgoing edge callables.
     *
     * @param array $pack Holds incoming and outgoing cargos.
     * @param string $name Method name. Queried among incoming and outgoing labels.
     * @param Direction $direction Direction in question; in or out.
     * 
     * @return bool
     */
    protected static function edgeCallableExists(
        array $pack,
        string $name,
        ID $id,
        Direction $direction 
        ): bool
    {
        return !is_null($pack[(string) $direction]->callable_edge_singularLabels) && in_array($name, $pack[(string) $direction]->callable_edge_singularLabels);
    }


    /**
     * "Has" Catcher for Edges 
     * 
     * @todo Check if it performs better with $edge->$node_adj()->equals($id) is first or last
     *
     * @param ParticleInterface $particle The particle that this handler is associated with.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name The edge node label in plural.
     * @param ID $id The ID of the node in question.
     * @param Direction  $direction The direction of the edge adjacent nodes to look up.
     * 
     * @return bool whether the node exists or not
     */
    protected static function checkEdgeNode(
        ParticleInterface $particle, 
        array $pack,
        string $name,
        ID $id,
        Direction $direction 
        ): bool
    {
        $direction = (string) $direction;
        $node_adj = static::ADJACENCY_EQUIVALENT[$direction];
        $cargo = $pack[$direction];
        $edges = $particle->edges()->$direction();
        foreach($edges as $edge) {
            if(
                $edge instanceof $cargo->singularLabel_class_pairs[$name] 
                && $edge->$node_adj()->equals($id)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * "Has" Catcher for Edge Callables
     * 
     * @todo Check if it performs better with $edge->id()->equals($id) is first or last
     *
     * @param ParticleInterface $particle The particle that this handler is associated with.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name The edge node label in plural.
     * @param ID $id The ID of the node in question.
     * @param Direction  $direction The direction of the edge adjacent nodes to look up.
     * 
     * @return bool whether the node exists or not
     */
    protected static function checkEdgeItself(
        ParticleInterface $particle, 
        array $pack,
        string $name,
        ID $id,
        Direction $direction 
        ): bool
    {
        $direction = (string) $direction;
        $cargo = $pack[$direction];
        $edges = $particle->edges()->$direction();
        foreach($edges as $edge) {
            if(
                $edge->id()->equals($id) &&
                $edge instanceof $cargo->callable_edge_singularLabel_class_pairs[$name]
            ) {
                return true;
            }
        }
        return false;
    }

}
