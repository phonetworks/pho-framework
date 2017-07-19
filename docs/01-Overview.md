# Overview

Pho-Framework is built upon [pho-lib-graph](https://github.com/phonetworks/pho-lib-graph) to constitute the basis of the [Pho stack](https://github.com/phonetworks/). Readers should study pho-lib-graph before starting with Pho Framework.

In Pho Framework, everything resides in Space, which is a direct extension of pho-lib-graph's Graph class. The framework nodes are called "particles" and they all must implement [ParticleInterface](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ParticleInterface.php).

[Space](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Space.php) is the [Graph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/Graph.php) equivalent of [pho-lib-graph](https://github.com/phonetworks/pho-lib-graph). It is the master graph, always stateless, and figuratively contains all nodes and edges, in all Pho installations. Though, no one can access it.

There are three types of particles in the Space:

1. [Actor](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Actor.php)
2. [Graph](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Graph.php) (which is the equivalent of pho-lib-graph's [SubGraph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/SubGraph.php))
3. [Object](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Object.php)

You may wonder why we've created confusion by renaming pho-lib-graph's Graph and SubGraph classes as Space and Graph respectively. That's because Graph and SubGraph are general Graph Theory concepts, which we didn't want to touch, and pho-lib-graph is meant to be a general-purpose graph theory library. On the other hand, in Pho universe, a social network itself is a **graph** that exists in a **space** along with many other social networks. In other words, a social network is a subgraph of the Space; e.g. Facebook is a subgraph of the Space, Twitter is a subgraph of the Space, and the list goes on. Calling all these networks, along with their subgraphs (think of Facebook Groups, Facebook Events, Twitter Lists, your contact list on Snapchat etc.) would create redundancy of the prefix "sub", hence we decided to call them all "graphs" and use the terms "subgraph" and "supergraph" to determine their positioning in respect to each other within the Pho universe.

## Actor
An actor does three things;

* read
* write
* subscribe

## Graph
Graph extends the SubGraph class of pho-lib-graph. Therefore it shows both graph and node properties. It does only one thing;
* contain

## Object
Object is what graph actors consume, and are centered around. Objects have one and only one edge:
* mention

To illustrate what these particles do with real-world examples;

* Users, admins and anonymous users of apps, social networks are **Actors**. They _do_ things; read, write, subscribe.
* Groups, events and social networks, friend lists are **Graphs**. They are recursive social graphs, they _contain_ Actors.
* Blog posts, status updates, Snaps, Tweets are all **Objects**. They are what social network members (Actors) are centered around. They optionally do one and only one thing; that is to _mention_. For example, a private message is an object that _mentions_ a certain actor, while a blog post is not.
