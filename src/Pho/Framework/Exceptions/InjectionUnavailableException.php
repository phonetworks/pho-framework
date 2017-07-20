<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Exceptions;

use Pho\Framework\ParticleInterface;

/**
 * Thrown with an attempt to access an injection that does not exist.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InjectionUnavailableException extends \Exception
{

    /**
     * Constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        parent::__construct();
        $this->message = sprintf("The injection %s does not exist", $key);
    }

}