# Architecture

In Pho-Framework architecture, the folder structure is as follows:

{ParticleName.php}
{ParticleName}Out/{EdgeName}.php

To illustrate this, take a look at [Object.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Object.php) and the [ObjectOut](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut) folder, where you can find its one and only edge; [Mention](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut/Mention.php).

The {ParticleName}Out folders may contain multiple edges. Again, take a look at the [ActorOut](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut) folder to see all three of Write, Read and Subscribe edges (and their notifications, predicates) in one place.

Besides the edges, the {ParticleName}Out folders may also contain predicates and notifications associated with each of these edges. These predicates and notifications may be stored either in the same file with the edge, or in different files within the same folder. So, in the example of the Actor's Write edge, we have:

* ActorOut/Write.php
* ActorOut/WritePredicate.php
* ActorOut/WriteNotification.php

the edge, its predicate, and notification, all in separate files. Alternatively, one could have stuck all three classes in one file e.g. Write.php, but the class naming must remain identical as to:

* **{ParticleName}Out/{EdgeName}Predicate** for predicates. (if available)
* **{ParticleName}Out/{EdgeName}Notification** for notifications. (if available)

To reiterate, the predicate and notification classes are optional.

Once you define an edge class (with the recommended directory structure above) you must register it via:

```registerOutgoingEdge(...string $class)```

Although  please note, the predicate and notification classes, if they are available, must be renamed in conformance to 
afore-mentioned requirements, and must reside in the same namespace with the edge class.
