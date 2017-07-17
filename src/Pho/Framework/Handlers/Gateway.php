<?php

namespace Pho\Framework\Handlers;

class Gateway
{

    /**
     * Holds handler info for incoming edges
     *
     * @var Cargo\IncomingEdgeCargo
     */
    public $cargo_in;

    /**
     * Holds handler info for outgoing edges
     *
     * @var Cargo\OutgoingEdgeCargo
     */
    public $cargo_out;

    protected function pack(): array
    {
        return [
            "in"  => $this->cargo_in,
            "out" => $this->cargo_out
        ];
    }

    public function handle(string $name, $args)/*:  mixed */
    {
        if(in_array($name, $this->cargo_out->setter_labels)) {
            return Set::handle($name, $args, $this->pack());
        }
        else if(in_array($name, $this->cargo_out->formative_labels)) {
            return Form::handle($name, $args, $this->pack());
        }
        else if(strlen($name) > 3) {
            $func_prefix = substr($name, 0, 3);
            $funcs = [
                "get"=>"Get::handle", 
                "has"=>"Has::handle"
            ];
            if (array_key_exists($func_prefix, $funcs) ) {
                try {
                    return $funcs[$func_prefix]($name, $args, $this->pack());
                }
                catch(Exceptions\InvalidParticleMethodException $e) {
                    throw $e;
                }
            }
        }
    }

    public function __call(string $name, array $args)
    {
        if(!array_key_exists($name, self::HANDLERS))
            return;
        $class = ucfirst($name);
        $class::handle($name, $args, $this);
    }
}