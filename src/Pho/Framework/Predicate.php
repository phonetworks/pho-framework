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

class Predicate extends AbstractPredicate {

    /**
     * A notifier edge sends notification to its
     * head node, following its creation.
     */
    const T_NOTIFIER = false;

    /**
     * A subscriber edge retrieves notification updates
     * from its head.
     */
    const T_SUBSCRIBER = false;

    /**
     * A consumer edge returns its head node when the
     * ```$edge->return()``` function is called. Otherwise,
     * it returns the edge itself.
     */
    const T_CONSUMER = false;
    
}