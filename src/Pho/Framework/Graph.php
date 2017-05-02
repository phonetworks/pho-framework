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
 * The Graph
 * 
 * This class is a shell to Pho\Lib\Graph's Graph implementation 
 * and it implements ContextInterface to give higher-level
 * software access to use both Frame and Graph as context objects. 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Graph extends \Pho\Lib\Graph\Graph implements ContextInterface {

    /**
     * The title of the graph.
     *
     * @var string
     */
    protected $title;

    /**
     * Constructor.
     * 
     * The title allows one to host multiple graphs in a single PHP instance.
     *
     * @param string $title Optional. Leave blank for a random title.
     */
    public function __construct(string $title = "") {
        if(empty($title)) {
            $this->title = uniqid("graph_", true);
        }
        else {
            $this->title = $title;                        
        }
    }

    /**
     * Returns the title of the graph.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function belongsOrEquals(ContextInterface $context): bool
    {
        return $context instanceof Graph && $this->title == $context->title();
    }

}