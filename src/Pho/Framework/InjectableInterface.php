<?php

/**
 * This file is part of the Phở package.
 * 
 * (c) Emre Sokullu <emre@phonetworks.org> 
 *
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code.
 */

namespace Pho\Framework;

/**
 * Injectable is a key extensibility comnponent of the Pho Framework.
 * 
 * This is defined in pho-framework, and not pho-lib-graph, because it is not
 * necessarily a core use-case of general purpose graphs.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface InjectableInterface
{
    /**
     * Injects a value to the subject
     * 
     * @param string $key The key to call the injectable
     * @param mixed $value The injectable itself.
     * 
     * @return void
     */
    public function inject(string $key, /*mixed*/ $value): void;
}