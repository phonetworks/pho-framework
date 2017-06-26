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
 * Read Predicate
 * 
 * We just define it and it extends \Pho\Lib\Graph\Predicate
 * nothing more.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class ReadPredicate extends \Pho\Framework\Predicate
{
    const T_CONSUMER = true;
}