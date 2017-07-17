<?php

namespace Pho\Framework\Handlers;

class Factory
{

    const HANDLERS = [ 
        "form" => "out", 
        "set" => "out", 
        "get" => "in", 
        "has" => "in"
    ];

    protected $cargo_in;
    protected $cargo_out;

    public function __call(string $name, array $args)
    {
        if(!array_key_exists($name, self::HANDLERS))
            return;
        $class = ucfirst($name);
        $cargo = "cargo_" . self::HANDLERS[$NAME];
        $class::handle($name, $args, $this->$cargo);
    }
}