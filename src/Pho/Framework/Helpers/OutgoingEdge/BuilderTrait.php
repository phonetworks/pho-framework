<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Helpers\OutgoingEdge;

use Pho\Framework;

/**
 * Helps set up the outgoing edges of a particle (aka node)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait BuilderTrait 
{

    use FormativePropertiesTrait;
    use SetterPropertiesTrait;


        /**
     * Getter Labels of Outgoing Edges
     * 
     * A simple array of head labels of outgoing edges in plural.
     * Labels in string format.
     *
     * @var array
     */
    protected $edge_out_getter_methods = [];

    /**
     * Getter Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges in plural as key,
     * associated class names as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_getter_classes = [];

    /**
     * Haser Labels of Outgoing Edges
     * 
     * A simple array of head labels of outgoing edges in singular.
     * Labels in string format.
     *
     * @var array
     */
    protected $edge_out_haser_methods = [];

    /**
     * Haser Classes of Outgoing Edges
     * 
     * An array of head labels of outgoing edges in singular as key,
     * associated class names as value.
     * Both in string format.
     *
     * @var array
     */
    protected $edge_out_haser_classes = [];

    /**
     * Sets up outgoing edges.
     * 
     * Given the configurations set in {ClassName}/{EdgeName}
     * classes , configures the way the class will act.
     *
     * @return void
     */
    protected function buildOutgoingEdges(Framework\ParticleInterface $particle): void
    {
        // !!! we use reflection method so that __DIR__ behaves properly with child classes.
        $self_reflector = new \ReflectionObject($particle);
        $edge_dir = dirname($self_reflector->getFileName()) . DIRECTORY_SEPARATOR . $self_reflector->getShortName() . "Out";  
        // !!! do not replace this with __DIR__

        if(!file_exists($edge_dir)) {
            Framework\Logger::warning("Edge directory %s does not exist", $edge_dir);
            return;
        }

        $locator = new \Zend\File\ClassFileLocator($edge_dir);
        foreach ($locator as $file) {
            $filename = str_replace($edge_dir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            foreach ($file->getClasses() as $class) {
                $this->registerOutgoingEdgeClass($class);
            }
        }
    }

    /**
     * Registers an Edge Out class that meets the requirements.
     *
     * @param string $class
     * 
     * @return void
     */
    public function registerOutgoingEdgeClass(string $class, int $trim = 2): void
    {
                $reflector = new \ReflectionClass($class);
                if(!$reflector->isSubclassOf(Framework\AbstractEdge::class)) { 
                    return;
                }

                $_method = (string) strtolower($reflector->getShortName());
                $_predicate = $class."Predicate";
                
                if($_predicate::T_FORMATIVE) {
                    $this->edge_out_formative_methods[] = $_method;
                    $this->edge_out_formative_edge_classes[$_method] = $class;
                    $formation_patterns = [];
                    foreach($reflector->getConstant("FORMABLES") as $settable) {
                        $pattern = "";
                        // @todo 
                        // we should do this with recursive
                        try{
                            $formable_params = 
                                (new \ReflectionMethod(
                                    $settable, 
                                    "__construct")
                                )->getParameters();
                        }
                        catch(\ReflectionException $e) {
                            $formable_params = 
                                (new \ReflectionMethod(
                                    get_parent_class($settable), 
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
                    $this->edge_out_formative_edge_patterns[$_method] = $formation_patterns;
                }
                else {
                    $this->edge_out_setter_methods[] = $_method;
                    $this->edge_out_setter_classes[$_method] = $class;
                    if($reflector->getConstant("SETTABLES_EXTRA")!==false)
                        $this->edge_out_setter_settables[$_method] = array_merge($reflector->getConstant("SETTABLES"),$reflector->getConstant("SETTABLES_EXTRA")) ;
                    else 
                        $this->edge_out_setter_settables[$_method] = $reflector->getConstant("SETTABLES") ;
                }

                $_method = $reflector->getConstant("HEAD_LABELS");
                $this->edge_out_getter_methods[] = $_method;
                $this->edge_out_getter_classes[$_method] = $class;
                $_method = $reflector->getConstant("HEAD_LABEL");
                $this->edge_out_haser_methods[] = $_method;
                $this->edge_out_haser_classes[$_method] = $class;
    }

}