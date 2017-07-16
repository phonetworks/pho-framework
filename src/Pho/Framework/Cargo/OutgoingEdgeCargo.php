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
 * Holds variables in regards to outgoing edges of a particle
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class OutgoingEdgeCargo extends AbstractOutgoingEdgeCargo
{
    use FormativePropertiesTrait;
    use SetterPropertiesTrait;
}