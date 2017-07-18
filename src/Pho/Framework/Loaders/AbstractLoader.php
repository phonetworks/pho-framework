<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Loaders;

use Pho\Framework\Cargo\AbstractCargo;
use Pho\Framework\ParticleInterface;


/**
 * In a nutshell, loaders pack (create) cargos (variable holders) and load 
 * them to particles (nodes).
 * 
 * Those cargos contain information on incoming and outgoing edges of the 
 * particle.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
abstract class AbstractLoader
{
    /**
     * Core cargo variable.
     *
     * @var \Pho\Framework\Cargo\AbstractCargo
     */
    public $cargo;

    /**
     * Constructor.
     * 
     * Set to be protected on purpose, 
     * so that it cannot be instantiated publicly.
     * 
     * @param array $data Any preliminary data, if available.
     */
    protected function __construct(array $data=[]) {
        $class = (new \ReflectionObject($this))->getName();
        $class = str_replace(["Loaders", "Loader"], "Cargo", $class);
        if(count($data)>0)
            $this->cargo  = new $class($data);
        else
            $this->cargo  = new $class;
    }

    /**
     * @param ParticleInterface $particle The particle that this loader is associated with.
     * 
     * @return AbstractLoader The loader object itself, so that the deploy command can be called.
     */
    abstract public static function pack(ParticleInterface $particle): AbstractLoader;
    
    /**
     * Assigns the private variable into the particle's given cargo variable.
     *
     * @param mixed $cargo The particle's own cargo variable. It's actually a nullable AbstractCargo (?AbstractCargo)
     * 
     * @return AbstractLoader self
     */
    public function deploy(/*AbstractCargo*/ &$cargo): AbstractLoader
    {
        $cargo = $this->cargo;
        return $this;
    }
}