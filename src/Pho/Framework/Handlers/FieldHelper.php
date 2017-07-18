<?php

namespace Pho\Framework\Handlers;

use Pho\Framework\ParticleInterface;
use Stringy\StaticStringy as Stringy;

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
     * @param ParticleInterface $particle Particle where we seek.
     * @param string $name Field name in question.
     * 
     * @return bool
     */
    public static function fieldExists(ParticleInterface $particle, string $name): bool
    {
        return (
            defined(get_class($particle)."::FIELDS") && 
            in_array(
                $name, 
                array_map(
                    "\Stringy\StaticStringy::upperCamelize", 
                    array_keys($particle::FIELDS)
                )
            )
        );
    }

    /**
     * Given the uppercamelized field name, find its normalized version in the fields section.
     *
     * @param ParticleInterface $particle Particle where we seek.
     * @param string $name Field name in question.
     * 
     * @return bool
     */
    public static function findFieldName(ParticleInterface $particle, string $name): string
        {
            $isset = function(string $name) use ($particle): bool
            {
                return isset($particle::FIELDS[$name]);
            };
            if($isset($name)) return $name; // upperCamelize
            elseif($isset(Stringy::camelize($name))) return Stringy::camelize($name);
            elseif($isset(Stringy::underscored($name))) return Stringy::underscored($name);
            throw new \Exception("Cannot resolve field name.");
        }

}