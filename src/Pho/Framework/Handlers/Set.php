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
use Pho\Framework\FieldHelper;
use Pho\Framework\Cargo\FieldsCargo;
use Pho\Framework\Exceptions\InvalidEdgeHeadTypeException;

/**
 * Setter Handler
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Set implements HandlerInterface
{

    /**
     * {@inheritDoc}
     * 
     * @throws \InvalidArgumentException thrown when there argument does not meet the constraints.
     */
    public static function handle(
        ParticleInterface $particle,
        array $pack,
        string $name, 
        array $args
        ) /*:  \Pho\Lib\Graph\EntityInterface*/
    {
        if( Utils::fieldExists($pack["fields"], substr($name, 3)) ) {
            return static::field($particle, $pack["fields"], substr($name, 3), $args);
        }
        $check = false;
        foreach($pack["out"]->setter_label_settable_pairs[$name] as $settable) {
            $check |= is_a($args[0], $settable);
        }
        if(!$check) { 
            throw new InvalidEdgeHeadTypeException($args[0], $pack["out"]->setter_label_settable_pairs[$name]);
        }
        $edge = new $pack["out"]->setter_classes[$name]($particle, array_shift($args));
        return $edge->fill($args)->return();
    }

    /**
     * Sets the field value
     *
     * @param ParticleInterface $particle
     * @param FieldsCargo $cargo
     * @param string $name Field name
     * @param array $args Field argument; [0] mixed, the argument. [1] ?bool defer_persist
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException thrown when there argument does not meet the constraints.
     */
    protected static function field(
        ParticleInterface $particle,
        FieldsCargo $cargo,
        string $name,
        array $value 
        ): void
    {
        $defer_persist = false;
        $defer_persist = ( isset($value[1]) && $value[1] == true );
        $value = $value[0];
        $name = Utils::findFieldName($cargo, $name);
        $field_helper = new FieldHelper($value, $cargo->fields[$name]);
        $field_helper->probe();
        $value = $field_helper->process(); 
        if(!$defer_persist) {
            $particle->attributes()->$name = $value;
            return;
        }
        $particle->attributes()->quietSet($name, $value);
    }
}