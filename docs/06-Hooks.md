# Hooks

Pho-Framework is built upon pho-lib-graph which has extensive support for hydration (via [hooks](https://github.com/phonetworks/pho-lib-graph/blob/master/docs/05-Hooks.md)) that can be used in several applications such as persistence. 

Pho-Framework adds up to that, by adding two new hooks ```creator()``` with ParticleTrait and ```edge()``` with AbstractNotification.

* **creator()**: called when ```creator()``` can't find the creator. Enables you to access ```$creator_id``` to fetch it from external sources. This can be used with any particle; be it an Actor, Object or Graph. The return value is **Actor**.

<!--
Also the following functions may be overridden with hydrating functions otherwise the program may not perform well at scale given the fact that the current implementation works by recursing through each and every edge of the given node.
-->

* **edge()**: called when ```edge()``` (in NotificationList.php) can't find the edge. Enables you to access ```$edge_id``` to fetch it from external sources. The return value is **\Pho\Lib\Graph\EdgeInterface**.
