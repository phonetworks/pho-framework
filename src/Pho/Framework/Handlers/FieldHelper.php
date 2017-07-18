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
 * Field related helper methods for Handler classes
 * 
 * Used by Get and Set.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class FieldHelper
{
    /**
     * Checks if there is such a field in the given particle.
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
     * Given the uppercamelized field name, find its normalized version in the fields section.
     *
     * @param FieldsCargo $cargo Fields cargo where we seek.
     * @param string $name Field name in question.
     * 
     * @return bool
     */
    public static function findFieldName(FieldsCargo $cargo, string $name): string
        {
            if(isset($cargo->fields[$name]))
                return $name;
            throw new \Exception("Cannot resolve field name.");
        }

}