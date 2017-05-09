<?php
/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/************************************************************
 * This script shows you how to interact with the Pho Framework
 *
 * @author Emre Sokullu
 ************************************************************/

 // 1. Initiate the autoloaders first.
require(__DIR__."/../vendor/autoload.php");

// 2. Let's create a graph.
use Pho\Framework;
$graph = new Framework\Graph();
echo "Graph created". PHP_EOL;

// 3. And now the actor
$actor = new Framework\Actor($graph);
printf("Actor created with id: %s", $actor->id());

function createObject($actor, $graph) {
  $object = new Framework\Object($actor, $graph);
  printf("Object created with id: %s", $object->id());
  return $actor->writes($object);
}

