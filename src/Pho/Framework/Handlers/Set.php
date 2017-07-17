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

use Pho\Framework\Exceptions\InvalidEdgeHeadTypeException;

class Set
{
    /**
     * Catch-all method for formers
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return \Pho\Lib\Graph\EntityInterface Returns \Pho\Lib\Graph\EdgeInterface by default, but in order to provide flexibility for higher-level components to return node (in need) the official return value is \Pho\Lib\Graph\EntityInterface which is the parent of both NodeInterface and EdgeInterface.
     */
    public static function handle(string $name, array $args): \Pho\Lib\Graph\EntityInterface
    {
        
        $class = self::findFormativeClass($name, $args);
        if(count($args)>0) {
            $head = new $class($this, $this->where($args), ...$args);
        }
        else {
            $head = new $class($this, $this->where($args));
        }
        $edge_class = $this->edge_out_formative_edge_classes[$name];
        $edge = new $edge_class($this, $head);
        return $edge->return();
    }

    protected static function findFormativeClass(string $name, array $args): string
    {
        $argline = "";
        if(count($args)>0) {
            foreach($args as $arg) {
                $argline .= sprintf(
                    "%s:::", 
                    str_replace("\\", ":", gettype($arg))
                );
            }
            $argline = substr($argline, 0, -3);
        }
        else {
            $argline = ":::";
        }

        foreach(
            $this->edge_out_formative_edge_patterns[$name] as 
            $formable=>$pattern
        ) {
            if(preg_match("/^".$pattern."$/", $argline)) {
                return $formable;
            }
        }

        throw new UnrecognizedSetOfParametersForFormativeEdgeException($argline, $this->edge_out_formative_edge_patterns[$name]);
    }

    /**
     * Catch-all method for setters
     *
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return \Pho\Lib\Graph\EntityInterface Returns \Pho\Lib\Graph\EdgeInterface by default, but in order to provide flexibility for higher-level components to return node (in need) the official return value is \Pho\Lib\Graph\EntityInterface which is the parent of both NodeInterface and EdgeInterface.
     */
    protected function _callSetter(string $name, array $args):  \Pho\Lib\Graph\EntityInterface
    {
        $check = false;
        foreach($this->edge_out_setter_settables[$name] as $settable) {
            $check |= is_a($args[0], $settable);
        }
        if(!$check) { 
            throw new InvalidEdgeHeadTypeException($args[0], $this->edge_out_setter_settables[$name]);
        }
        $edge = new $this->edge_out_setter_classes[$name]($this, $args[0]);
        return $edge->return();
    }
}