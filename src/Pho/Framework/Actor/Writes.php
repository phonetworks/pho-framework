<?php

namespace Pho\Framework\Actor;

use Pho\Framework;

class Writes extends Subscribes {

    const HEAD_LABEL = "write";
    const HEAD_LABELS = "writes";
    const TAIL_LABEL = "writer";
    const TAIL_LABELS = "writers";
    const SETTABLES = [Framework\Frame::class, Framework\Object::class]; /* inherits the values in Edits */

}