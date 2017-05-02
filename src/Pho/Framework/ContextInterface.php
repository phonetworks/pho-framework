<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework;

/**
 * ContextInterface
 * 
 * This class is a shell to Pho\Lib\Graph's GraphInterface implementation.
 * It is used by all Framework particles and graphs that can be used 
 * as a context object in higher level packages. 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface ContextInterface extends \Pho\Lib\Graph\GraphInterface {

    /**
     * Checks if the given context is equal to or a subelement of this context.
     *
     * @param ContextInterface $context
     * @return bool
     */
    public function belongsOrEquals(ContextInterface $context): bool;

}