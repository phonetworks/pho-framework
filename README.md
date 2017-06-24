# Pho-Framework

Pho-Framework is the foundational component of Pho Stack. It establishes
the object-centered actor/graph framework that all Pho components are built upon. It is stateless, which means, it doesn't provide persistence of its objects in any way, but it is designed for such extensibility via hydrator functions.


## Install

The recommended way to install pho-framework is through composer.

```composer require phonetworks/pho-framework```

## Documentation

Pho-Framework is built upon [pho-lib-graph](https://github.com/phonetworks/pho-lib-graph) to constitute the basis of the [Pho stack](https://github.com/phonetworks/). Readers should study pho-lib-graph before starting with Pho Framework.

In Pho Framework, everything resides in Space, which is a direct extension of pho-lib-graph's Graph class. The framework nodes are called "particles" and they all must implement [ParticleInterface](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ParticleInterface.php).

There are three types of particles:

1. Actor
2. Graph (which is the equivalent of pho-lib-graph's SubGraph)
3. Object

You may wonder why we've created confusion by renaming pho-lib-graph's Graph and SubGraph classes as Space and Graph respectively. That's because Graph and SubGraph are general Graph Theory concepts, which we didn't want to touch, and pho-lib-graph is meant to be a general-purpose graph theory library. On the other hand, in Pho universe, a social network itself is a **graph** that exists in a **space** along with many other social networks. In other words, a social network is a subgraph of the Space; e.g. Facebook is a subgraph of the Space, Twitter is a subgraph of the Space, and the list goes on. Calling all these networks, along with their subgraphs (think of Facebook Groups, Facebook Events, Twitter Lists, your contact list on Snapchat etc.) would create redundancy of the prefix "sub", hence we decided to call them all "graphs" and use the terms "subgraph" and "supergraph" to determine their positioning in respect to each other within the Pho universe.

### Actor
An actor does three things;

* read
* write
* subscribe

### Graph
Graph extends the SubGraph class of pho-lib-graph. Therefore it shows both graph and node properties. It does only one thing;
* contain

### Object
Object is what graph actors consume, and are centered around. Objects have one and only one edge:
* transmit

To illustrate what these particles do with real-world examples;

* Users, admins and anonymous users of apps, social networks are **Actors**. They _do_ things; ready, write, subscribe.
* Groups, events and social networks, friend lists are **Graphs**. They are recursive social graphs, they _contain_ Actors.
* Blog posts, status updates, Snaps, Tweets are all **Objects**. They are what social network members (Actors) are centered around. They optionally do one and only one thing; that is to _transmit_. For example, a private message is an object that _transmits_ to a certain actor, while a blog post is not.

## Architecture

In Pho-Framework architecture, the folder structure is as follows:

{ParticleName.php}
{ParticleName}Out/{EdgeName}.php

To illustrate this, take a look at [Actor.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Actor.php) and the [ActorOut](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut) folder.

## Creating & Extending Particles

The function below defines the edges that this particle accepts:

```php
$this->registerIncomingEdges(ActorOut\Write::class);
```

This must be called in the constructor, before calling the parent particle's constructor. Any edge that claims that this particle is its tail, and that has not been already registered by the particle's parent, must be defined here, otherwise an exception will be thrown.

Secondly, all outgoing edges of a particle must be defined in the {ParticleName}Out/ folder.

An examplary edge is shown below:

```php
class Subscribe extends Framework\AbstractEdge {
    const HEAD_LABEL = "subscription";
    const HEAD_LABELS = "subscriptions";
    const TAIL_LABEL = "subscriber";
    const TAIL_LABELS = "subscribers";
    const SETTABLES = [Framework\ParticleInterface::class];
}
```

For an edge to be valid, it must:
* extend Framework\AbstractEdge
* have five different constants where:
    * TAIL_LABEL: what the tail node of this edge's role is called, in singular. A *subscriber* subscribes. So it's "subscriber"
    * TAIL_LABELS: same as above, in plural. So it's "subscribers"
    * HEAD_LABEL: what the head node of this edge's role is called, in singular. A subscriber subscribes to a *subscription*, hence it's "subscription"
    * HEAD_LABELS: same as above, in plural. So it's "subscriptions"
    * SETTABLES: what classes can this edge target, in array format. If it's [Framework\ParticleInterface::class], that means it can target any node/particle. Sometimes this level of flexibility may not be the case for all types of edges; for example, the [Write](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/Write.php) edge cannot target Actor particles, because a user can't create a user. Hence its SETTABLES is declared as [Framework\Object::class, Framework\Graph::class] only, so that it can target Graphs and Objects only, and not Actors.
    
As you can see above, the constants defined in the edge class are merely for naming purposes. The mechanics function as follows;

```php 
// $actor will be our Actor node 
$actor = new Actor($graph);

// This is a set function, generate automatically from the ActorOut/Susbcribes.php edge. 
$actor->subscribes($content);

// This is a ActorOut/Subscribes.php edge getter where $actor is in "head node" position, and it retrieves its tails.
// again generated automatically.
$actor->getSubscribers();

// This is a ActorOut/Subscribes.php edge getter where $actor is in "tail node" position, and it retrieves its heads.
// again generated automatically.
$actor->getSubscriptions();
```

## Extending Particles for Hydration

Pho-Framework is built upon pho-lib-graph which has extensive support for hydration that can be used for several applications such as persistence. Pho-Framework adds up to that, by adding a new hydrating function ```hydratedCreator()```.

* **hydratedCreator()**: called when ```creator()``` can't find the creator. Enables you to access ```$creator_id``` to fetch it from external sources. This can be used with any particle; be it an Actor, Object or Graph. The return value is **Actor**.

Also the following functions may be overridden with hydrating functions otherwise the program may not perform well at scale given the fact that the current implementation works by recursing through each and every edge of the given node.

* **__callGetterEdgeIn(string $name)**: Example: called with ```getSubscribers()``` for incoming edges of "Subscribe". $name would resolve as "subscribers" after going through strtolower and trim operations. You may fetch the associated class names with ```$this->edge_in_getter_classes[$name]```. The return value is **array\<EdgeInterface\>**. Current implementation is as follows:

```php
protected function __callGetterEdgeIn(string $name): array
    {
        $edges_in = $this->edges()->in();
        $return = [];
        array_walk($edges_in, function($item, $key) use (&$return, $name) {
            if($item instanceof $this->edge_in_getter_classes[$name])
                $return[] = $item->tail()->node();
        });
        return $return;
    }
```

* **__callGetterEdgeOut(string $name)**: Example: called with ```getSubscriptions()``` for outgoing edges of "Subscribe". $name would resolve as "subscriptions" after going through strtolower and trim operations. You may fetch the associated class names with ```$this->edge_out_getter_classes[$name]```. The return value is **array\<EdgeInterface\>**. Current implementation is as follows:

```php
protected function __callGetterEdgeOut(string $name): array
    {
        $edges_out = $this->edges()->out();
        $return = [];
        array_walk($edges_out, function($item, $key) use (&$return, $name) {
            if($item instanceof $this->edge_out_getter_classes[$name])
                $return[] = $item();
        });
        return $return;
    }
```
* **__callHaserEdgeIn(ID $id, string $name)**: Example: called with ```hasSubscriber()``` for incoming edges of "Subscribe". $name would resolve as "subscriber" after going through strtolower and trim operations. You may fetch the associated class names with ```$this->edge_in_haser_classes[$name]```. The return value is **bool**. Current implementation is as follows:

```php
protected function __callHaserEdgeIn(ID $id, string $name): bool
    {
        $edges_in = $this->edges()->in();
        foreach($edges_in as $edge) {
            if($edge instanceof $this->edge_in_haser_classes[$name] && $edge->tailID()->equals($id))
                return true;
        }
        return false;
    }
```

* **__callHaserEdgeOut(ID $id, string $name)**: Example: called with ```hasSubscription()``` for outgoing edges of "Subscribe". $name would resolve as "subscription" after going through strtolower and trim operations. You may fetch the associated class names with ```$this->edge_out_haser_classes[$name]```. The return value is **bool**. Current implementation is as follows:

```php
protected function __callHaserEdgeOut(ID $id, string $name): bool
    {
        $edges_out = $this->edges()->out();
        foreach($edges_out as $edge) {
            if($edge instanceof $this->edge_out_haser_classes[$name] && $edge->headID()->equals($id))
                return true;
         }
        return false;
    }
```

* **__callSetter(string $name, array $args)**: Example: called with ```subscribes()``` to set up a new edge of "Subscribes". $name would resolve as "subscribes" after going through a strtolower operation. You may fetch the associated class names with ```$this->edge_out_setter_settables[$name]```. Framework returns the Edge but in order to provide flexibility for higher level components, the return value is **\Pho\Lib\Graph\EntityInterface** which is the parent of both NodeInterface and EdgeInterface. Current implementation is as follows:

```php
protected function _callSetter(string $name, array $args): \Pho\Lib\Graph\EntityInterface
{
        $check = false;
        foreach($this->edge_out_setter_settables[$name] as $settable)
            $check |= is_a($args[0], $settable);
        if(!$check) 
            throw new InvalidEdgeHeadTypeException($args[0], $this->edge_out_setter_settables[$name]);
        $edge = new $this->edge_out_setter_classes[$name]($this, $args[0]);
        return $edge;
        // return $edge(); // returns the head() // not at framework level.
}
```

<!--
## Reference

Valid methods in the Pho Framework stack are:
-->

