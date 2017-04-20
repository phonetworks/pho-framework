<?php

namespace Pho\Framework;

class Frame extends SubGraph implements NodeInterface {

    const EDGES_IN = [Actor\Reads::class, Actor\Subscribes::class, Actor\Writes::class, Object\Transmits::class];

}