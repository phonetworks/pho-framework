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
use Pho\Framework\Cargo\FieldsCargo;
use Pho\Framework\Exceptions\InvalidEdgeHeadTypeException;
use Webmozart\Assert\Assert;

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
        $edge = new $pack["out"]->setter_classes[$name]($particle, $args[0]);
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
        if( isset($value[1]) && $value[1] )
            $defer_persist = true;
        $value = $value[0];
        $name = Utils::findFieldName($cargo, $name);
        if(isset($cargo->fields[$name]["constraints"])) {
                static::probeField($cargo->fields[$name]["constraints"], $value);
        }
        $value = static::applyDirectives($value, $cargo->fields[$name]);
        if($defer_persist) {
            $particle->attributes()->$name = $value;
        }
        $particle->attributes()->quietSet($name, $value);
    }

    /**
     * Applies directives to the value to return
     * 
     * @param mixed $value The value to check.
     * @param array $directives Particle directives.
     * 
     * @return mixed 
     */
    protected static function applyDirectives(/*mixed*/ $value, array $field_settings) /*: mixed*/
    {
        if(!isset($field_settings["directives"]))
            return $value;
        $directives = $field_settings["directives"];
        $isDirectiveEnabled = function(string $param) use($directives): bool
        {
            return (isset($directives[$param]) && $directives[$param]);
        };
        if($isDirectiveEnabled("md5")) {
            return md5($value);
        }
        return $value;
    }

    /**
     * Checks if the field meets the requirements of the constraints in the 
     * particle's  FIELDS constant.
     * 
     * @param array $constraints
     * @param [type] $field_value
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException thrown when there argument does not meet the constraints.
     */
    protected static function probeField(array $constraints, $field_value): void
    {
        foreach($constraints as $constraint=>$constraint_val) {
            if(is_null($constraint_val))
                continue;
            switch($constraint) {
                case "minLength":
                case "maxLength":
                case "greaterThan":
                case "lessThan":
                    Assert::$constraint($field_value, $constraint_val);
                    break;
                case "uuid":
                    Assert::$constraint($field_value);
                    break;
                case "regex":
                    Assert::$constraint($field_value, "/".addslashes($constraint_val)."/");
                    break;
             } 
        }
    }
}