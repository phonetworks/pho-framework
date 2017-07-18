<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Handlers;

use Pho\Framework\ParticleInterface;

/**
 * Handler Gateway
 * 
 * Handler gateways are connected to particles and decide
 * what static class to call for each catch-all method request. 
 * 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Gateway
{

    /**
     * The particle that this handler is associated with.
     *
     * @var Pho\Framework\ParticleInterface
     */
    protected $particle;

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

    /**
     * Constructor.
     *
     * @param ParticleInterface $particle  The particle that this handler is associated with.
     */
    public function __construct(ParticleInterface $particle) {
        $this->particle = $particle;
    }

    /**
     * Packs cargo variables
     *
     * Cargo variables are then transported to relevant static
     * handler class.
     * 
     * @return array An array of incoming edge / outgoing edge cargo
     */
    protected function pack(): array
    {
        return [
            "in"  => $this->cargo_in,
            "out" => $this->cargo_out
        ];
    }

    /**
     * Catch-call handler switch
     * 
     * Decides what static class to call for the given catch-all method.
     * 
     * @param string $name Catch-all method name.
     * @param array $args Catch-all method arguments.
     * 
     * @throws \Pho\Framework\Exceptions\InvalidParticleMethodException when the given method does not match with anything.
     * @throws \InvalidArgumentException thrown when there argument does not meet the constraints.
     */
    public function switch(string $name, array $args) /*:  \Pho\Lib\Graph\EntityInterface*/
    {
        if(in_array($name, $this->cargo_out->setter_labels)) {
            return Set::handle($this->particle, $this->pack(), $name, $args);
        }
        else if(in_array($name, $this->cargo_out->formative_labels)) {
            return Form::handle($this->particle, $this->pack(), $name, $args);
        }
        else if(strlen($name) > 3) {
            $func_prefix = substr($name, 0, 3);
            $funcs = [
                "get"=> __NAMESPACE__ . "\\Get::handle", 
                "has"=> __NAMESPACE__ . "\\Has::handle",
                "set"=> __NAMESPACE__ . "\\Set::handle",
            ];
            if (array_key_exists($func_prefix, $funcs) ) {
                try {
                    return $funcs[$func_prefix]($this->particle, $this->pack(), $name, $args);
                }
                catch(\Pho\Framework\Exceptions\InvalidParticleMethodException $e) {
                    throw $e;
                }
            }
        }
    }
}