<?php

namespace Pho\Framework\Actor;

use Pho\Framework;

class Subscribes extends Framework\AbstractEdge {

    const HEAD_LABEL = "subscription";
    const HEAD_LABELS = "subscriptions";
    const TAIL_LABEL = "subscriber";
    const TAIL_LABELS = "subscribers";
    const SETTABLES = [Framework\NodeInterface::class];

}