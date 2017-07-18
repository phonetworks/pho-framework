<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Cargo;

/**
 * Fields Cargo
 * 
 * Holds variables regarding Fields.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class FieldsCargo implements CargoInterface
{
    /**
     * @var array An array with field names as key, constraints and directives as values in array form.
     */
    public $fields = [];

}