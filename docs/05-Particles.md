# Particles

Edges must be defined at object construction via 
* ```registerIncomingEdges(...string $class)``` 
* and ```registerOutgoingEdges(...string $class)```

Any edge that claims that this particle is its tail, and that has not been already registered by the particle's parent, must be defined, otherwise an exception will be thrown when trying to access edge methods.

Secondly, you can define field constraints and directives for particles by setting up a **FIELDS** constant in the particle head.

```php
class CustomObject extends Object
{
    const FIELDS = [
        "my_field" => [
            "constraints" => [
                "minLength" => null, // minimum character length (int)
                "maxLength" => null, // maximum character length (int)
                "uuid" => null, // whether the field must be in uuid format or not (bool) 
                "regex" => null, // the regular expression the value must satisfy (string without enclosure)
                "greaterThan" => null, // minimum integer value to enter (int)
                "lessThan" => null, // maximum integere value to enter (int)
            ],
            "directives" => [
                "md5" => true, // encrypt the value by MD5
                "now" => false,
                "default" => "",
            ]
        ]
    ];

```

Thus, the CustomObject particle above will by default come with two additional methods (getters and setters):

* **getMyField()**
* **setMyField(/\*mixed\*/ $value, bool $silent = false)**

With Fields, "Directives" help set up a default value, or filter the passed argument. For example, the "md5" field will encrypt all plaintext arguments passed to the setMyField function. As for "Constraints", they make sure the argument values meet certain requirements.

> When ```$silent``` is on, no "modified" signal is emitted with AttributeBag value changes.

> While defining field names (such as my_field in the example above) make sure they are either underscored (my_field_name) or
> camelcased (myFieldName) in terms of formatting. Pho Framework reorganizes the variable names to match camelcased function
> name format, thus it functions properly only if your field names parseable.

Last but not least, all outgoing edges of a particle must be defined in the {ParticleName}Out/ folder.

An example edge is shown below:

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
    * **TAIL_LABEL**: what the tail node of this edge's role is called, in singular. A *subscriber* subscribes. So it's "subscriber"
    * **TAIL_LABELS**: same as above, in plural. So it's "subscribers"
    * **HEAD_LABEL**: what the head node of this edge's role is called, in singular. A subscriber subscribes to a *subscription*, hence it's "subscription"
    * **HEAD_LABELS**: same as above, in plural. So it's "subscriptions"
    * **SETTABLES**: what classes can this edge target, in array format. If it's [Framework\ParticleInterface::class], that means it can target any node/particle. Sometimes this level of flexibility may not be the case for all types of edges; for example, the [Write](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/Write.php) edge cannot target Actor particles, because a user can't create a user. Hence its SETTABLES is declared as [Framework\Object::class, Framework\Graph::class] only, so that it can target Graphs and Objects only, and not Actors.
    * **SETTABLES_EXTRA**: similar to SETTABLES, but allows a subclass to define new settables that extend the parent class' ones, without overriding them (because defining a new SETTABLES constant would override the parents')
    * **FORMABLES**: if it's a formative edge (as defined by the predicate) the formables constant defines the classes that this edge can create. Please note, Formables cannot point to an interface, and **must** point to classes with a constructor.
    
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
