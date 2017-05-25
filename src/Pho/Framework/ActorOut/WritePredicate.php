<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\ActorOut;


/**
 * Write Predicate
 * 
 * Please note that the write predicate is binding, hence
 * it must be defined.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class WritePredicate extends \Pho\Lib\Graph\Predicate {
    protected $binding = true;
}