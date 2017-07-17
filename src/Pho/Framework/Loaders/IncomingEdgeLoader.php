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
 * Helps set up the incoming edges of a particle (aka node)
 * 
 * {@inheritDoc}
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class IncomingEdgeLoader extends AbstractLoader
{
    /**
     * Sets up incoming edges.
     * 
     * Given the configurations set in the particle class itself 
     * (e.g. EDGES_IN constant), configures the way the 
     * class will act.
     *
     * {@inheritDoc}
     */
    public static function pack(ParticleInterface $particle): AbstractLoader 
    {
        $obj = new IncomingEdgeLoader($particle->getRegisteredIncomingEdges());
        foreach($obj->cargo->classes as $class) {
            $class_ref = new \ReflectionClass($class);
            $check = false;
            foreach($class_ref->getConstant("SETTABLES") as $head_node_type) {
                $check |= is_a($particle, $head_node_type);
            }
            if($class_ref->getConstant("SETTABLES_EXTRA")!==false) {
                foreach($class_ref->getConstant("SETTABLES_EXTRA") as $head_node_type) {
                    $check |= is_a($particle, $head_node_type);
                }
            }
            if($check) {
                $method = $class_ref->getConstant("TAIL_LABELS");
                $obj->cargo->labels[] = $method;
                $obj->cargo->label_class_pairs[$method] = $class;
                
                $method = $class_ref->getConstant("TAIL_LABEL");
                $obj->cargo->singularLabels[] = $method;
                $obj->cargo->singularLabel_class_pairs[$method] = $class;
            }
        }
        return $obj;
    }
}