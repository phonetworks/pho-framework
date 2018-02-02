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

/**
 * Handler related static helper methods.
 * 
 * Used by Get and Set.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Utils
{
    /**
     * Checks if there is such a field in the given particle.
     * 
     * Used by Get and Set.
     *
     * @param FieldsCargo $cargo Fields cargo where we seek.
     * @param string $name Field name in question.
     * 
     * @return bool
     */
    public static function fieldExists(FieldsCargo $cargo, string $name): bool
    {
        return array_key_exists($name, $cargo->fields);
    }

    /**
     * Given the uppercamelized field name, find its normalized version 
     * in the fields section.
     * 
     * Used by Get and Set.
     *
     * @param FieldsCargo $cargo Fields cargo where we seek.
     * @param string $name Field name in question.
     * 
     * @return bool
     */
    public static function findFieldName(FieldsCargo $cargo, string $name): string
        {
            $name = \Stringy\StaticStringy::upperCamelize($name);
            if(self::fieldExists($cargo, $name))
                return $name;
            throw new \Exception("Cannot resolve field name.");
        }
    
    /**
     * A helper method to pick a single element only.
     *
     * Used by Get
     *
     * @param ?array $elements
     *
     * @return mixed A single element or null
     */
    public static function pickSingular(?array $elements) /*:mixed*/
    {
        if(!is_null($elements) && is_array($elements) && count($elements) >= 1)
            return $elements[0];
        return $elements;
    }

}
