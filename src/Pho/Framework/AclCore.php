<?php

namespace Pho\Framework;

use Pho\Lib\Graph;

class AclCore {

    protected $creator;
    protected $context;

    public function __construct(Actor $creator, Graph\GraphInterface $context) {
        $this->creator = $creator;
        $this->context = $context;
    }

    public function toArray(): array
    {
        //eval(\Psy\sh());
        return [
            "creator" => (string) $this->creator->id(),
            "context" => ($this->context instanceof Graph\Graph) ? Graph\Graph::class : (string) $this->context->id()
        ]; 
    }

}