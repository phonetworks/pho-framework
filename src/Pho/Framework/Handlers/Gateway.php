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
use Pho\Framework\Exceptions\InvalidParticleMethodException;

/**
 * Handler Gateway
 * 
 * Handler gateways are connected to particles and decide
 * what static class to call for each catch-all method request. 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Gateway
{

    /**
     * Default Handler adapters
     *
     * @var array
     */
    protected $adapters = [
        "form" => "\\" . __NAMESPACE__ . "\\Form", 
        "get"  => "\\" . __NAMESPACE__ . "\\Get", 
        "has"  => "\\" . __NAMESPACE__ . "\\Has",
        "set"  => "\\" . __NAMESPACE__ . "\\Set"
    ];

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
     * Holds handler info for fields
     *
     * @var Cargo\FieldsCargo
     */
    public $cargo_fields;

    /**
     * Constructor.
     *
     * @param ParticleInterface $particle  The particle that this handler is associated with.
     */
    public function __construct(ParticleInterface $particle) {
        $this->particle = $particle;
    }

    /**
     * Registers a new handler adapter.
     *
     * Default handlers may be overriden.
     * 
     * @param string $key Adapter key; e.g. "get", "set", "form" etc.
     * @param string $class Handler class to register. A handler class shall implement HandlerInterface
     * 
     * @return void
     */
    public function registerHandlerAdapter(string $key, string $class): void
    {
        $this->adapters[$key] = $class;
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
            "in"     => $this->cargo_in,
            "out"    => $this->cargo_out,
            "fields" => $this->cargo_fields
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
        $method = function(string $key) {
            return $this->adapters[$key] . "::handle";
        };

        if(in_array($name, $this->cargo_out->setter_labels)) {
            return call_user_func($method("set"), $this->particle, $this->pack(), $name, $args);
        }
        elseif(in_array($name, $this->cargo_out->formative_labels)) {
            return call_user_func($method("form"), $this->particle, $this->pack(), $name, $args);
        }
        elseif(strlen($name) > 3) {
            $func_prefix = substr($name, 0, 3);
            if (array_key_exists($func_prefix, $this->adapters) ) {
                try {
                    return call_user_func($method($func_prefix), $this->particle, $this->pack(), $name, $args);
                }
                catch(\Exception $e) {
                    throw $e;
                }
            }
        }
        throw new InvalidParticleMethodException(__CLASS__, $name);
    }
}