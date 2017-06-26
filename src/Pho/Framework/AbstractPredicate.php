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

abstract class AbstractPredicate extends \Pho\Lib\Graph\Predicate
{
    /**
     * methods that would be available in this class
     *
     * @var array
     */
    public $_methods;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $oClass = new \ReflectionObject($this);
        $this->_methods = array_map(function(string $name) {
                return substr(strtolower($name),2);
            }, array_filter(
                    array_keys(
                        $oClass->getConstants()), 
                        function(string $name) {
                            return substr($name,0,2) == "T_";
                        }
                )
        );
    }

    /**
     * {@internal}
     *
     * Used to call predicate traits.
     */
    public function __call(string $name, array $args)//: mixed
    {
        if(in_array($name, $this->_methods)) {
            $const = sprintf("static::T_%s", strtoupper($name));
            return constant($const);
        }
    }
}