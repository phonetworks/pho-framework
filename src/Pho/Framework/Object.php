<?php

namespace Pho\Framework;

class Object extends AbstractNode /* !!DOES NOT!! implements ObjectInterface */ {

    const EDGES_IN = [Actor\Reads::class, Actor\Subscribes::class, Actor\Writes::class, Object\Transmits::class];

}