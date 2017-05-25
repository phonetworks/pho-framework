## Important Note

In contrast to Actor and Object, Frame has no regular edges built-in.

Frame extends Pho\Lib\SubGraph, hence comes with the **"contains"** 
method which is a quasi-edge, but not quite.

Nevertheless, Frame is a node *-- in the sense that it implements both
Pho\Lib\Graph\NodeInterface and Pho\Framework\ParticleInterface--* as well as
a subgraph (with Pho\Lib\Graph\ClusterTrait) both at the same time.

For more information, check out the internals of [Pho\Lib\Graph](https://github.com/phonetworks/pho-lib-graph)