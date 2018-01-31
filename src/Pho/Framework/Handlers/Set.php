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
 * A setter may have two forms;
 * * ```setSomething()``` which would be used for Fields, where Something is a Field.
 * * ```something()``` which would be used for outgoing edges.
 * 
 * In this class we don't modify $name arguments (unlike what we do in Form, Get and Has classes)
 * because we expect the method to be called in the proper format, and:
 * * for Fields, they are already stored in the particle in upper-camelized format.
 * * for outgoing edges, they are already stored in the particle in camlized format.
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
        // don't camelize or upper-camelize $name yet because it is supposed to
        // come in upper-camlized format, and that's how they are stored by 
        // Loaders\FieldLoader.php
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
        $edge = new $pack["out"]->setter_classes[$name]($particle, array_shift($args), null, ...$args);
        return $edge->return();
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
        static::saveField($particle, $name, $value, $defer_persist, $field_helper);
    }
    
    /**
     * Helper method to save the field
     * 
     * This method exists to help extending the "field" method without overriding it.
     *
     * @param ParticleInterface $particle
     * @param string $field_name Field name
     * @param mixed $field_value Field value
     * @param bool $defer_persist Whether to defer persistence
     * @param FieldHelper $helper an object full of information re: this field.
     * 
     * @return void
     *
     * @throws \InvalidArgumentException thrown when there argument does not meet the constraints.
     */
    protected static function saveField(
        ParticleInterface $particle, 
        string $field_name, 
        /*mixed*/ $field_value, 
        bool $defer_persist, 
        FieldHelper $helper): void
    {

        if(!$defer_persist) {
            $particle->attributes()->$field_name = $field_value;
            return;
        }
        $particle->attributes()->quietSet($field_name, $field_value);
    }
}
