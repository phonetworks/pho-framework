<?php

namespace Pho\Framework\Object;

use Pho\Framework;

class Transmits extends Framework\AbstractEdge {

    const HEAD_LABEL = "receiver";
    const HEAD_LABELS = "receivers";
    const TAIL_LABEL = "transmitter";
    const TAIL_LABELS = "transmitters";

    const SETTABLES = [Framework\NodeInterface::class];

}