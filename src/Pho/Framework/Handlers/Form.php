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
        $head = static::formHead(...func_get_args());
        $edge_class = $pack["out"]->formative_label_class_pairs[$name];
        $edge = new $edge_class($particle, $head);
        return $edge->return();
    }

    /**
     * Forms the head particle.
     *
     * @param ParticleInterface $particle The particle that this handler is working on.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return \Pho\Lib\Graph\NodeInterface
     */
    protected static function formHead(
        ParticleInterface $particle,
        array $pack,
        string $name, 
        array $args): \Pho\Lib\Graph\NodeInterface
    {
        $class = static::findFormativeClass($name, $args, $pack);
        if(count($args)>0) {
            return new $class($particle, $particle->where(), ...$args);
        }
        return new $class($particle, $particle->where());
    }

    /**
     * Based on given arguments, helps find the matching class to form.
     * 
     * **IMPORTANT NOTE for developers;**
     * 
     * We don't touch the $name argument. Because a call like:
     * ```$user->createGroup()```
     * means that the formative edge is CreateGroup
     * and it was already registered in the particle with "camelize"
     * function. So camelizing calls would allow methods like
     * ```$user->create_group()``` or ```$user->CreateGroup``` also
     * take the same effect. This is not the behavior we want.
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
                $_type = ((gettype($arg) == "boolean") ? "bool" : gettype($arg));
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
