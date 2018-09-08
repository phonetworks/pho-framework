<?php declare(strict_types=1);
/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Pho\Framework;

use Pho\Lib\Graph\Node;
use Pho\Lib\Graph\Edge;
use Pho\Lib\Graph\SubGraph;
use Pho\Lib\Graph\EntityInterface;

/**
 * {@inheritDoc}
 */
class ID extends \Pho\Lib\Graph\ID
{
    /**
     * Fetches the entity header.
     * 
     * Entity headers will be as follows:
     * 
     * * 0: Graph
     * * 1: Unidentified Node
     * * 2: SubGraph Node
     * * 3: Framework\Graph Node
     * * 4: Actor Node
     * * 5: Object Node
     * * 6: Unidentified Edge
     * * 7: Read Edge
     * * 8: Write Edge
     * * 9: Subscribe Edge
     * * 10: Mention Edge
     * * 11: Unidentified
     * 
     * This method may be overriden by packages at higher levels.
     * The purpose of headers is to enable easy/fast classification
     * of entitities by looking up the first byte of the UUID.
     * 
     * @param EntityInterface $entity
     * 
     * @return array An array of two ints (actually hexadecimals) 
     */
    protected static function header(EntityInterface $entity): array
    {
        error_log("calculating headers: ");
        error_log(get_class($entity));
        $first_hex = 11;
        if($entity instanceof Node) {
            if($entity instanceof Obj)
                $first_hex =  5;
            elseif($entity instanceof Actor)
                $first_hex =  4;
            elseif($entity instanceof Graph)
                $first_hex =  3;
            elseif($entity instanceof SubGraph)
                $first_hex =  2;
            else 
                $first_hex =  1;
        }
        elseif($entity instanceof Edge) {
            // order is important
            if($entity instanceof ObjOut\Mention)
                $first_hex =  10;
            elseif($entity instanceof ActorOut\Write)
                $first_hex = 8;
            elseif($entity instanceof ActorOut\Subscribe)
                $first_hex = 9;
            elseif($entity instanceof ActorOut\Read)
                $first_hex = 7;
            else
                $first_hex = 6;
        }
        return [$first_hex, rand(0,15)];
    }
}