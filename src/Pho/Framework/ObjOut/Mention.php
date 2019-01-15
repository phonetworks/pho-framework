<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\ObjOut;

use Pho\Framework;

/**
 * Transmits Edge
 * 
 * **"Transmits"** is the only outgoing edge of the 
 * {@link Pho\Framework\Obj} particle. It links the
 * Obj with other nodes, allowing message passing.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Mention extends Framework\AbstractEdge
{

    const HEAD_LABEL = "mention";
    const HEAD_LABELS = "mentions";
    const TAIL_LABEL = "mentioner";
    const TAIL_LABELS = "mentioners";

    const SETTABLES = [Framework\ParticleInterface::class];

    protected function execute(): void
    {
        /*
        $notification_class = static::NOTIFICATION;
        $notification = new $notification_class($this);
        if(!$notification instanceof Framework\Notification) {
            throw new Framework\Exceptions\NotificationNotFoundException(get_class($this));
        }*/
        if(
            isset($this->notification) &&
            !$this->orphan() &&
            $this->head()->node() instanceof Framework\Actor 
        )
                $this->head()->node()->notify($this->notification);
                
    }

}