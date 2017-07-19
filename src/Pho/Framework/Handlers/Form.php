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
use Pho\Framework\Exceptions\UnrecognizedSetOfParametersForFormativeEdgeException;

/**
 * Particle Former Handler
 * 
 * This is similar to setters, except that this also forms an actual
 * particle (aka node)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Form implements HandlerInterface
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
        $class = static::findFormativeClass($name, $args, $pack);
        $head = static::formHead($particle, $args);
        $edge_class = $pack["out"]->formative_label_class_pairs[$name];
        $edge = new $edge_class($particle, $head);
        return $edge->return();
    }

    /**
     * Forms the head particle.
     *
     * @param ParticleInterface $particle
     * @param array $args
     * 
     * @return void
     */
    protected static function formHead(
        ParticleInterface $particle, 
        array $args): void
    {
        if(count($args)>0) {
            $head = new $class($particle, $particle->where(), ...$args);
        }
        else {
            $head = new $class($particle, $particle->where());
        }
    }

    /**
     * Based on given arguments, helps find the matching class to form.
     *
     * @param string $name
     * @param array $args
     * @param array $pack
     * 
     * @return string The class name.
     */
    protected static function findFormativeClass(
        string $name, 
        array $args, 
        array $pack
        ): string
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
            $pack["out"]->formative_patterns[$name] as $formable=>$pattern
        ) {
            if(preg_match("/^".$pattern."$/", $argline)) {
                return $formable;
            }
        }

        throw new UnrecognizedSetOfParametersForFormativeEdgeException($argline, $pack["out"]->formative_patterns[$name]);
    }
}