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

use Pho\Framework;
use Pho\Framework\Cargo\OutgoingEdgeCargo;
use Pho\Framework\ParticleInterface;

/**
 * Helps set up the incoming edges of a particle (aka node)
 * 
 * {@inheritDoc}
 * 
 * Outgoing edges are stored in particle camelized; e.g. 
 * * birthday becomes birthday
 * * join_time becomes joinTime
 * * joinTime remains joinTime
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class OutgoingEdgeLoader extends AbstractLoader
{
    /**
     * Sets up outgoing edges.
     * 
     * Given the configurations set in {ClassName}/{EdgeName}
     * classes , configures the way the class will act.
     *
     * {@inheritDoc}
     */
    public static function pack(Framework\ParticleInterface $particle): AbstractLoader
    {
        $obj = new OutgoingEdgeLoader($particle->getRegisteredOutgoingEdges());
        foreach ($obj->cargo->classes as $class) {
            $class = str_replace("ObjectOut", "ObjOut", $class); // for backwards-compatibility.
            $cargo = &$obj->cargo;
            $reflector = new \ReflectionClass($class);
                if(!$reflector->isSubclassOf(Framework\AbstractEdge::class)) { 
                    continue;
                }
                $_method = (string) \Stringy\StaticStringy::camelize($reflector->getShortName());
                $_predicate = $class."Predicate";
                if($_predicate::T_FORMATIVE) {
                    $cargo->formative_labels[] = $_method;
                    $cargo->formative_label_class_pairs[$_method] = $class;
                    $formation_patterns = [];
                    foreach($reflector->getConstant("FORMABLES") as $formable) {
                        $formable_reflection = new \ReflectionClass($formable);
                        $__method = $_method . \Stringy\StaticStringy::upperCamelize($formable_reflection->getShortName());
                        $cargo->formative_labels[] = $__method;
                        $cargo->formative_label_class_pairs[$__method] = $class;
                        $pattern = "";
                        // @todo 
                        // we should do this with recursive
                        try {
                            $formable_params = 
                                (new \ReflectionMethod(
                                    $formable, 
                                    "__construct")
                                )->getParameters();
                        }
                        catch(\ReflectionException $e) {
                            $formable_params = 
                                (new \ReflectionMethod(
                                    get_parent_class($formable), 
                                    "__construct"
                                    )
                                )->getParameters();
                        }
                        $formative_trim = self::getFormativeTrim($particle);
                        for($i=0;$i<$formative_trim;$i++) {
                            @array_shift($formable_params);
                        }
                        if(count($formable_params)==0) {
                            $formation_patterns[$formable] = ":::";
                            continue;
                        }
                        foreach($formable_params as $param) {
                            $_pattern = "";
                            if(!$param->hasType())
                                $_pattern .= ".+?";
                            else
                                $_pattern .= $param->getType();
                            $_pattern .= ":::";
                            if($param->isOptional()) {
                                if(strlen($pattern)>3&&substr($pattern, -3)==":::") // if it's the first optional param, and not the first param
                                    $pattern = substr($pattern, 0, -3) .sprintf("(:::%s)?", $_pattern);
                                elseif(strlen($pattern)>5&&substr($pattern, -5)==":::)?") // if it comes after an optional param
                                    $pattern = substr($pattern, 0, -5) .sprintf(")?(:::%s)?", $_pattern);
                                else // if it's the first argument
                                    $pattern .= sprintf("(%s)?", $_pattern);
                            }
                            else
                                $pattern .= $_pattern;
                        }
                        $pattern = str_replace("\\", ":", $pattern);
                        if($pattern[strlen($pattern)-1]!="?") {
                            $pattern = substr($pattern, 0 ,-3);
                        }
                        elseif($pattern[0]!="(") { 
                            // if the last char is optional
                            // like: string:::string:::string?
                            // instead of string:::string:::(string:::)?
                            // which would be trimmed as string:::string:::(string::
                            // show: string:::string(:::string?)
                            $pattern = str_replace(sprintf(":::(%s)?", $_pattern), sprintf("(:::%s)?", substr($_pattern, 0, -3)), $pattern); 
                        }
                        else { // case of (string:::)?
                            $pattern = str_replace(":::)?", ")?", $pattern);
                        }
                        $formation_patterns[$formable] = $pattern;
                        $cargo->formative_patterns[$__method] = [$formable=>$pattern];
                    }
                    $cargo->formative_patterns[$_method] = $formation_patterns;
                }
                else {
                    $cargo->setter_labels[] = $_method;
                    $cargo->setter_classes[$_method] = $class;
                    if($reflector->getConstant("SETTABLES_EXTRA")!==false)
                        $cargo->setter_label_settable_pairs[$_method] = 
                            array_merge(
                                $reflector->getConstant("SETTABLES"),
                                $reflector->getConstant("SETTABLES_EXTRA")
                            ) ;
                    else 
                        $cargo->setter_label_settable_pairs[$_method] = 
                            $reflector->getConstant("SETTABLES") ;
                }
                $_method = \Stringy\StaticStringy::camelize($reflector->getConstant("HEAD_LABELS"));
                $cargo->labels[] = $_method;
                $cargo->label_class_pairs[$_method] = $class;
                $_method = \Stringy\StaticStringy::camelize($reflector->getConstant("HEAD_LABEL"));
                $cargo->singularLabels[] = $_method;
                $cargo->singularLabel_class_pairs[$_method] = $class;

                if($reflector->hasConstant("TAIL_CALLABLE_LABELS")) {
                    $callable = $reflector->getConstant("TAIL_CALLABLE_LABELS");
                    $cargo->callable_edge_labels[] = \Stringy\StaticStringy::camelize($callable);
                    $cargo->callable_edge_label_class_pairs[$callable] = $class;
    
                    $callable = $reflector->getConstant("TAIL_CALLABLE_LABEL");
                    $cargo->callable_edge_singularLabels[] = \Stringy\StaticStringy::camelize($callable);
                    $cargo->callable_edge_singularLabel_class_pairs[$callable] = $class;
                }
        }
        return $obj;
    }

    /**
     * Calculates how many arguments in constructor to skip
     *
     * Used with formative predicates.
     * The default value is 2 for framework, 3 for microkernel.
     * 
     * @param Framework\ParticleInterface $particle
     * 
     * @return int
     */
    protected static function getFormativeTrim(Framework\ParticleInterface $particle): int
    {
        $trim = 2;
        if(defined(get_class($particle)."::FORMATIVE_TRIM_CUT"))
            $trim = $particle::FORMATIVE_TRIM_CUT;
        return $trim;
    }
    
}
