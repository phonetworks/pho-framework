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
        if($name==='id') {
            return $particle->id();
        }
        elseif(static::edgeMethodExists($pack, $name, Direction::out())) {
            return static::getEdgeNodes($particle, $pack, $name, Direction::out());
        }   
        elseif(static::edgeMethodExistsSingular($pack, $name, Direction::out())) {
            return Utils::pickSingular(static::getEdgeNodes($particle, $pack, $name, Direction::out(), true));
        }   
        elseif(static::edgeMethodExists($pack, $name, Direction::in())) {
            return static::getEdgeNodes($particle, $pack, $name, Direction::in());
        }
        elseif(static::edgeMethodExistsSingular($pack, $name, Direction::in())) {
            return Utils::pickSingular(static::getEdgeNodes($particle, $pack, $name, Direction::in(), true));
        }
        elseif(static::edgeCallableExists($pack, $name, Direction::out())) {
            return static::getEdgeItself($particle, $pack, $name, Direction::out());
        }  
        elseif(static::edgeCallableExistsSingular($pack, $name, Direction::out())) {
            return Utils::pickSingular(static::getEdgeItself($particle, $pack, $name, Direction::out(), true));
        } 
        elseif(static::edgeCallableExists($pack, $name, Direction::in())) {
            return static::getEdgeItself($particle, $pack, $name, Direction::in());
        }
        elseif(static::edgeCallableExistsSingular($pack, $name, Direction::in())) {
            return Utils::pickSingular(static::getEdgeItself($particle, $pack, $name, Direction::in(), true));
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
    protected static function edgeMethodExists(
        array $pack,
        string $name,
        Direction $direction 
        ): bool
    {
        return in_array($name, $pack[(string) $direction]->labels);
    }
    
    /**
     * Whether the given method is available for incoming or outgoing edges.
     *
     * Works similarly to ```edgeMethodExists``` with the difference of looking
     * up singular labels. Example:
     *
     * > ```getComment()``` is the same as ```getComments()[0]```
     *
     * @see edgeMethodExists
     *
     * @param array $pack Holds incoming and outgoing cargos.
     * @param string $name Method name. Queried among incoming and outgoing labels.
     * @param Direction $direction Direction in question; in or out.
     * 
     * @return bool
     */
    protected static function edgeMethodExistsSingular(
        array $pack,
        string $name,
        Direction $direction 
        ): bool
    {
        return in_array($name, $pack[(string) $direction]->singularLabels);
    }

    /**
     * Whether the given method is available for incoming or outgoing edge callable.
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
        Direction $direction 
        ): bool
    {
        return !is_null($pack[(string) $direction]->callable_edge_labels) && in_array($name, $pack[(string) $direction]->callable_edge_labels);
    }
    
    /**
     * Whether the given method is available for incoming or outgoing edge callable.
     *
     * Works similarly to ```edgeCallableExists``` with the difference of looking
     * up singular labels. Example:
     *
     * > ```getComment()``` is the same as ```getComments()[0]```
     *
     * @see edgeCallableExists
     *
     * @param array $pack Holds incoming and outgoing cargos.
     * @param string $name Method name. Queried among incoming and outgoing labels.
     * @param Direction $direction Direction in question; in or out.
     * 
     * @return bool
     */
    protected static function edgeCallableExistsSingular(
        array $pack,
        string $name,
        Direction $direction 
        ): bool
    {
        return !is_null($pack[(string) $direction]->callable_edge_singularLabels) 
                && 
                in_array($name, $pack[(string) $direction]->callable_edge_singularLabels)
        ;
    }


    /**
     * Getter Catcher for Edges In and Out
     *
     * @param ParticleInterface $particle The particle that this handler is associated with.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name The edge node label in plural.
     * @param Direction  $direction The direction of the edge adjacent nodes to look up.
     * @param bool  $singular Is this for a singular call
     * 
     * @return array The edge nodes.
     */
    protected static function getEdgeNodes(
        ParticleInterface $particle, 
        array $pack,
        string $name,
        Direction $direction,
        bool $singular = false 
        ): array
    {
        // $name = strtolower($name); // this is now taken care of beforehand.
        $direction = (string) $direction;
        $node_adj = static::ADJACENCY_EQUIVALENT[$direction]; // in->tail, out->head
        $cargo = $pack[$direction];
        $edges = $particle->edges()->$direction();
        $haystack = $singular ? "singularLabel_class_pairs" : "label_class_pairs";
        $return = [];
        array_walk(
            $edges, function ($item, $key) use (&$return, $name, $cargo, $node_adj, $haystack) {
                if($item instanceof $cargo->$haystack[$name]) {
                    $return[] = $item->$node_adj()->node();
                }
            }
        );
        return $return;
    }

    /**
     * Getter Catcher for Edge Callables In and Out
     *
     * @param ParticleInterface $particle The particle that this handler is associated with.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name The edge node label in plural.
     * @param Direction  $direction The direction of the edge adjacent nodes to look up.
     * @param bool $singular Is this for a singular call
     * 
     * @return array The edge nodes.
     */
    protected static function getEdgeItself(
        ParticleInterface $particle, 
        array $pack,
        string $name,
        Direction $direction,
        bool $singular = false
        ): array
    {
        // $name = strtolower($name); // this is now taken care of beforehand.
        $direction = (string) $direction;
        $cargo = $pack[$direction];
        $haystack = $singular ? "callable_edge_singularLabel_class_pairs" : "callable_edge_label_class_pairs";
        $edges = $particle->edges()->$direction($cargo->$haystack[$name]);
        return array_values(iterator_to_array($edges));
    }

}
