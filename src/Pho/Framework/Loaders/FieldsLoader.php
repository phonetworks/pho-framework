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

use Pho\Framework\Cargo\IncomingEdgeCargo;
use Pho\Framework\ParticleInterface;

/**
 * Helps set up the fields of a particle (aka node)
 * 
 * {@inheritDoc}
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class FieldsLoader extends AbstractLoader
{
    /**
     * Sets up fields.
     * 
     * Given the configurations set in the particle fields 
     * (e.g. FIELDS constant), configures the way the 
     * class will act.
     *
     * {@inheritDoc}
     */
    public static function pack(ParticleInterface $particle): AbstractLoader 
    {
        $obj = new FieldsLoader;
        $obj->fields = [];
        $fields = [];

        if(!defined(get_class($particle)."::FIELDS"))
            $fields = [];
        elseif(is_array($particle::FIELDS))
            $fields = $particle::FIELDS;
        else
            $fields = json_decode($particle::FIELDS, true);

        foreach($fields as $key=>$value) {
            $obj->cargo->fields[
                \Stringy\StaticStringy::upperCamelize($key)
            ] = $value;
        }

        

        return $obj;
    }
}