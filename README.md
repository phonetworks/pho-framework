# Pho-Framework

Pho-Framework is the foundational component of Pho Stack. It establishes
the object-centered actor/graph framework that all Pho compoenets are built upon.


## Install

The recommended way to install pho-framework is through composer.

```composer require phonetworks/pho-framework```

## Documentation

Pho-Framework is built upon [pho-lib-graph](https://github.com/phonetworks/pho-lib-graph) to constitute the basis of the [Pho stack](https://github.com/phonetworks/). Readers should study pho-lib-graph before starting with Pho Framework.

With Pho, the framework nodes are called "particles" and they all implement ParticleInterface.

There are three types of particles:

1. Actor
2. Frame
3. Object

### Actor
An actor does three things;

* read
* write
* subscribe

### Frame
Frame extends the SubGraph class of pho-lib-graph. Therefore it shows both graph and node properties. It does only one thing;
* contains

### Object
Object is what graph actors consume, and are centered around. Objects have one and only one edge:
* transmit

## Architecture

In Pho-Framework architecture, the folder structure is as follows:

{ParticleName.php}
{ParticleName}Out/{EdgeName}.php

To illustrate this, take a look at Actor.php and the Actor folder.

## Creating & Extending Particles

The line below defines the edges that this particle accepts:

```php
const EDGES_IN = [ActorOut\Reads::class, ActorOut\Subscribes::class, ObjectOut\Transmits::class];
```

Any edge that claims that this particle is its tail, must be listed here, otherwise the {....} exception will be thrown.

All outgoing edges of a particle must be defined in the {ParticleName}Out/ folder.

An examplary edge is shown below:

```php
class Reads extends Framework\AbstractEdge {
    const HEAD_LABEL = "read";
    const HEAD_LABELS = "reads";
    const TAIL_LABEL = "reader";
    const TAIL_LABELS = "readers";
    const SETTABLES = [Framework\ParticleInterface::class];
}
```

For an edge to be valid, it must:
* extend Framework\AbstractEdge
* have five different constants:
* HEAD_LABEL

Once it is set this way, it may be called:
$actor->get...

## Extending Particles for Hydration

Pho-Framework is built upon pho-lib-graph which has extensive support for hydration that can be used for several applications such as persistence. Pho-Framework adds up to that, by adding a new hydrating function ```hydratedCreator()```.

* **hydratedCreator()**: called when ```creator()``` can't find the creator. Enables you to access ```$creator_id``` to fetch it from external sources. This can be used with any particle; be it an Actor, Object or Frame. The return value is **Actor**.

## Reference

Valid methods in the Pho Framework stack are:

