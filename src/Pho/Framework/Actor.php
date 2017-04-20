<?php

namespace Pho\Framework;

class Actor extends AbstractNode /* !!DOES NOT!! implements ActorInterface */ {

    const EDGES_IN = [Actor\Reads::class, Actor\Subscribes::class, Object\Transmits::class];

}