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
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class OutgoingEdgeLoader
{
    /**
     * Sets up outgoing edges.
     * 
     * Given the configurations set in {ClassName}/{EdgeName}
     * classes , configures the way the class will act.
     *
     * {@inheritDoc}
     */
    protected function pack(Framework\ParticleInterface $particle): AbstractLoader
    {
        $obj = new OutgoingEdgeLoader();
        // !!! we use reflection method so that __DIR__ behaves properly with child classes.
        $self_reflector = new \ReflectionObject($particle);
        $edge_dir = 
            dirname($self_reflector->getFileName()) . 
            DIRECTORY_SEPARATOR . 
            $self_reflector->getShortName() 
            . "Out";  
        // !!! do not replace this with __DIR__

        if(!file_exists($edge_dir)) {
            Framework\Logger::warning("Edge directory %s does not exist", $edge_dir);
            return $obj;
        }

        $locator = new \Zend\File\ClassFileLocator($edge_dir);
        foreach ($locator as $file) {
            $filename = str_replace($edge_dir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            foreach ($file->getClasses() as $class) {
                self::registerOutgoingEdgeClass($obj, $class);
            }
        }
        return $obj;
    }

    /**
     * Registers an Edge Out class that meets the requirements.
     *
     * @param OutgoingEdgeLoader $obj The object to fill data with
     * @param string $class
     * @param int $trim how many arguments in constructor to skip
     * 
     * @return void
     */
    protected static function registerOutgoingEdgeClass(OutgoingEdgeLoader & $obj, string $class, int $trim = 2): void
    {
                $reflector = new \ReflectionClass($class);
                if(!$reflector->isSubclassOf(Framework\AbstractEdge::class)) { 
                    return;
                }

                $_method = (string) strtolower($reflector->getShortName());
                $_predicate = $class."Predicate";
                
                if($_predicate::T_FORMATIVE) {
                    $obj->cargo->formative_labels[] = $_method;
                    $obj->cargo->formative_label_class_pairs[$_method] = $class;
                    $formation_patterns = [];
                    foreach($reflector->getConstant("FORMABLES") as $formable) {
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
                        for($i=0;$i<$trim;$i++) {
                            @array_shift($formable_params);
                        }
                        if(count($formable_params)==0) {
                            $formation_patterns[$settable] = ":::";
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
                                $pattern .= sprintf("(%s)?", $_pattern);
                            }
                            else
                                $pattern .= $_pattern;
                        }
                        $formation_patterns[$settable] = substr(str_replace("\\", ":", $pattern),0 ,-3);
                    }
                    $obj->cargo->formative_patterns[$_method] = $formation_patterns;
                }
                else {
                    $obj->cargo->setter_labels[] = $_method;
                    $obj->cargo->setter_classes[$_method] = $class;
                    if($reflector->getConstant("SETTABLES_EXTRA")!==false)
                        $obj->cargo->setter_label_settable_pairs[$_method] = 
                            array_merge(
                                $reflector->getConstant("SETTABLES"),
                                $reflector->getConstant("SETTABLES_EXTRA")
                            ) ;
                    else 
                        $obj->cargo->setter_label_settable_pairs[$_method] = 
                            $reflector->getConstant("SETTABLES") ;
                }

                $_method = $reflector->getConstant("HEAD_LABELS");
                $obj->cargo->labels[] = $_method;
                $obj->cargo->label_class_pairs[$_method] = $class;
                $_method = $reflector->getConstant("HEAD_LABEL");
                $obj->cargo->singularLabels[] = $_method;
                $obj->cargo->singularLabel_class_pairs[$_method] = $class;
    }
}