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

use Pho\Lib\Graph\ID;

class Get
{
    /**
     * Catch-all method for getters
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return array An array of ParticleInterface objects
     * 
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    public static function handle(string $name, array $args, Gateway $carrier): array
    {
        $name = strtolower(substr($name, 3));
        if(in_array($name, $carrier->cargo_out->labels)) {
            return self::handleEdgeOut($name);
        }   
        else if(in_array($name, $this->edge_in_getter_methods)) {
            return self::handleEdgeIn($name);
        }
        throw new Exceptions\InvalidParticleMethodException(__CLASS__, $name);
    }


    /**
     * Getter Catcher for Edges Out
     *
     * @param string $name Representation of nodes to retrieve
     * 
     * @return array The edges.
     */
    protected function __callGetterEdgeOut(string $name): array
    {
        $edges_out = $this->edges()->out();
        $return = [];
        array_walk(
            $edges_out, function ($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_out_getter_classes[$name]) {
                    $return[] = $item();
                }
            }
        );
        return $return;
    }

    /**
     * Getter Catcher for Edges In
     *
     * @param string $name Representation of nodes to retrieve
     * 
     * @return array The edges.
     */
    protected function __callGetterEdgeIn(string $name): array
    {
        $edges_in = $this->edges()->in();
        $return = [];
        array_walk(
            $edges_in, function ($item, $key) use (&$return, $name) {
                if($item instanceof $this->edge_in_getter_classes[$name]) {
                    $return[] = $item->tail()->node();
                }
            }
        );
        return $return;
    }


    /**
     * Catch-all method for hasers -hasSomething()-
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments. Must contain a single ID for the queried object, or it will throw an exception.
     * 
     * @return bool whether the node exists or not
     * 
     * @throws InvalidArgumentException when the method is called without a single ID object as argument.
     * @throws Exceptions\InvalidParticleMethodException when no matching method found.
     */
    protected function _callHaser(string $name, array $args): bool
    {
        if(!isset($args[0]) || !$args[0] instanceof ID) {
            throw new \InvalidArgumentException(
                sprintf('The function %s must be called with a single argument that is strictly a \Pho\Lib\Graph\ID object', $name)
            );
        }
        $id = $args[0];
        $original_name = $name;
        $name = strtolower(substr($name, 3));
        if(in_array($name, $this->edge_out_haser_methods)) {
            return $this->__callHaserEdgeOut($id, $name);
        }   
        else if(in_array($name, $this->edge_in_haser_methods)) {
            return $this->__callHaserEdgeIn($id, $name);
        }
        throw new Exceptions\InvalidParticleMethodException(__CLASS__, $original_name);
    }


    /**
     * Haser Catcher for Edges Out
     *
     * @param string $name Representation of nodes to check
     * 
     * @return bool whether the node exists or not
     */
    protected function __callHaserEdgeOut(ID $id, string $name): bool
    {
        $edges_out = $this->edges()->out();
        foreach($edges_out as $edge) {
            if($edge instanceof $this->edge_out_haser_classes[$name] && $edge->headID()->equals($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Haser Catcher for Edges In
     *
     * @param string $name Representation of nodes to check
     * 
     * @return bool whether the node exists or not
     */
    protected function __callHaserEdgeIn(ID $id, string $name): bool
    {
        $edges_in = $this->edges()->in();
        foreach($edges_in as $edge) {
            if($edge instanceof $this->edge_in_haser_classes[$name] && $edge->tailID()->equals($id)) {
                return true;
            }
        }
        return false;
    }
}