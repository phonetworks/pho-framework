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

/**
 * Handler Interface
 * 
 * Handlers handle catch-all methods of particles (aka nodes)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface HandlerInterface
{
    /**
     * Catch-all handler
     *
     * @param ParticleInterface $particle The particle that this handler is working on.
     * @param array  $pack Holds cargo variables extracted by loaders.
     * @param string $name Catch-all method name
     * @param array  $args Catch-all method arguments
     * 
     * @return mixed "Has"=>bool. Get=>array. Set/Form=>\Pho\Lib\Graph\EdgeInterface by default, but in order to provide flexibility for higher-level components to return node (in need) the official return value is \Pho\Lib\Graph\EntityInterface which is the parent of both NodeInterface and EdgeInterface.
     */
    public static function handle(
            ParticleInterface $particle,
            array $pack,
            string $name,
            array $args

    ) /*: \Pho\Lib\Graph\EntityInterface */ // it can be an array or bool in the cases of getter and has respectively.
    ;
}