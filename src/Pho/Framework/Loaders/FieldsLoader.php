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
use Pho\Lib\Graph\EntityInterface;

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
     * Fields are stored in particle upper camelized; e.g. 
     * * birthday becomes Birthday
     * * join_time becomes JoinTime
     * * joinTime becomes JoinTime
     *
     * {@inheritDoc}
     */
    public static function pack(ParticleInterface $particle): AbstractLoader 
    {
        $obj = new FieldsLoader;
        $obj->fields = [];
        $fields = [];

        $obj->cargo->fields = self::fetchArray($particle);

        return $obj;
    }
    
    /**
     * Helper method for pack()
     *
     * @param Particle $particle
     *
     * @return array An array where keys are upper-camelized
     */
    public static function fetchArray(EntityInterface $entity): array
    {
        if(!defined(get_class($entity)."::FIELDS"))
            return [];
        elseif(is_array($entity::FIELDS))
            $fields = $entity::FIELDS;
        else
            $fields = json_decode($entity::FIELDS, true);

        $ret = [];
        foreach($fields as $key=>$value) {
            $ret[
                \Stringy\StaticStringy::upperCamelize($key)
            ] = $value;
        }
        
        return $ret;
    }
}
