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

/**
 * The Space
 * 
 * This class is a shell to Pho\Lib\Graph's Graph implementation 
 * and it implements ContextInterface to give higher-level
 * software access to use both Frame and Graph as context objects. 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Space extends \Pho\Lib\Graph\Graph implements ContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function in(ContextInterface $context): bool
    {
        return $context instanceof Space;
    }

}