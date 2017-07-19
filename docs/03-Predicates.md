# Predicates

Predicates must extend the Predicate class in Pho-Framework. A predicate has four configurable traits:

1. **Notifier**: a notifier edge, sends a Notification to its head node.
2. **Subscriber**: a subscriber edge, makes its tail listen to notification updates from its head node.
3. **Consumer**:  a consumer edge, once its "return()" function called, would return the head node in response, and not the edge itself.
4. **Binding**: a binding edge, once deleted, would also delete its head node.
5. **Formative**: with formative edges, you don't define a head node, because it is created dynamically at the same time with edge construction.

You can learn the traits of a predicate by calling the boolean methods;

```php
$predicate->notifier();
$predicate->subscriber();
$predicate->consumer();
$predicate->binding();
$predicate->formative();
```

Or, you can do that from the edge, with:

```php
$edge->predicate()->notifier();
$edge->predicate()->subscriber();
$edge->predicate()->consumer();
$edge->predicate()->binding();
$edge->predicate()->formative();

```

The edges notifier, subscriber, consumer, binding, formative characteristics are set by their respective class files. To learn more about it, check out [Predicate.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/Predicate.php) and pho-framework level implementations:

* [ActorOut/WritePredicate](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/WritePredicate.php): adopts subscriber and binding traits.
* [ActorOut/ReadPredicate](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/ReadPredicate.php): adopts consumer trait:
* [ActorOut/SubscribePredicate](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/SubscribePredicate.php): adopts subscriber trait.
* [ObjectOut/MentionPredicate](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut/MentionPredicate.php): adopts notifier trait.
