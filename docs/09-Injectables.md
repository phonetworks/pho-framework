# Injectables

Objects that implement the [InjectableInterface](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/InjectableInterface.php) and use the [InjectableTrait](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/InjectableTrait.php) are easily extensible, with a plug-in variable architecture.

In order to inject a variable to an injectable object, just use

```php
$obj->inject("key", $booster);
```

Then, the ```$obj``` will be able to use the ```$booster``` object internally via:

```php
$this->injection("key");
```

The use Injectable is discouraged, as it may represent security holes if not used properly. But you can use it when you must. Currently the only class that implements it by default is [AbstractEdge](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/AbstractEdge.php).
