<?php

namespace Pho\Framework\Actor;

use Pho\Framework;

class Reads extends Framework\AbstractEdge {

    const HEAD_LABEL = "read";
    const HEAD_LABELS = "reads";
    const TAIL_LABEL = "reader";
    const TAIL_LABELS = "readers";
    const SETTABLES = [Framework\NodeInterface::class];

}