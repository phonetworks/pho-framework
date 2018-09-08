<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework;

use Webmozart\Assert\Assert;
use Valitron\Validator;
use Pho\Lib\Graph as LibGraph;

/**
 * Helps set up fields.
 * 
 * Used by AbstractEdge and Handlers\Set (for Nodes)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class FieldHelper
{

    protected $value;
    protected $settings;

    /**
     * whether the field has index
     *
     * @var boolean
     */
    protected $with_index = false;

    /**
     * whether the field is set to be unique
     *
     * @var boolean
     */
    protected $is_unique = false;

    /**
     * Constructor.
     * 
     * @param mixed $value The value to check.
     * @param array $settings Particle field settings.
     */
    public function __construct(/*mixed*/ $value, array $settings)
    {
        $this->value = $value;
        $this->settings = $settings;
    }

    /**
     * Whether the field has index on it.
     *
     * @return boolean
     */
    public function withIndex(): bool
    {
        return $this->with_index;
    }

    /**
     * Whether the field must be unique
     *
     * @return boolean
     */
    public function isUnique(): bool
    {
        return $this->is_unique;
    }

    /**
     * Applies directives to the value to return
     * 
     * @return mixed 
     */
    public function process() /*: mixed*/
    {
        if(!isset($this->settings["directives"]))
            return $this->value;
        $directives = $this->settings["directives"];
        $isDirectiveEnabled = function(string $param) use($directives): bool
        {
            return (isset($directives[$param]) && $directives[$param]);
        };

        if($isDirectiveEnabled("index")) {
            $this->with_index = true;
        }

        if($isDirectiveEnabled("unique")) {
            $this->is_unique = true;
        }

        if($isDirectiveEnabled("sha1")) {
            return sha1($this->value);   
        }
        elseif($isDirectiveEnabled("md5")) {
            return md5($this->value);
        }

        return $this->value;
    }

    /**
     * Checks if the field meets the requirements of the constraints in the 
     * particle's FIELDS constant.
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException thrown when there argument does not meet the constraints.
     */
    public function probe(): void
    {
        if(!isset($this->settings["constraints"])) 
            return;
        $constraints = $this->settings["constraints"];
        foreach($constraints as $constraint=>$constraint_val) {
            if(is_null($constraint_val))
                continue;
            switch($constraint) {
                case "format":
                    if(!in_array($constraint_val, [
                         "numeric",
                         "ip",
                         "email",
                         "url",
                         "creditCard",
                         "alpha",
                         "alphaNum"
                         // "date" // there is already the Date field
                    ])) {
                        /*
                        if($constraint_val == "uuid" || $constraint_val == "udid") {
                            try {
                                ID::fromString($this->value);
                            }
                            catch(LibGraph\Exceptions\MalformedIDException $e) {
                                throw new \InvalidArgumentException;
                            }
                        }
                        else {
                            throw new \InvalidArgumentException;
                        }
                        */
                        throw new \InvalidArgumentException;
                    }
                    $v = new Validator(["field"=>$this->value]);
                    $v->rule($constraint_val, "field");
                    if(!$v->validate()) {
                        throw new \InvalidArgumentException;
                    }
                    break;
                //case "dateFormat":
                case "dateBefore":
                case "dateAfter":
                    $v = new Validator(["field"=>$this->value]);
                    $v->rule($constraint, "field", $constraint_val);
                    if(!$v->validate()) {
                        throw new \InvalidArgumentException;
                    }
                    break;
                case "minLength":
                case "maxLength":
                case "greaterThan":
                case "lessThan":
                    Assert::$constraint($this->value, $constraint_val);
                    break;
                               
                case "id":
                    try {
                        ID::fromString($this->value);
                    }
                    catch(LibGraph\Exceptions\MalformedIDException $e) {
                        throw new \InvalidArgumentException;
                    }
                    break;
                
                case "regex":
                    /*
                    if($constraint_val[0]!="/")
                        Assert::$constraint($this->value, "/".addslashes($constraint_val)."/");
                    else*/
                        Assert::$constraint($this->value, $constraint_val);
                    break;
             } 
        }
    }
}
