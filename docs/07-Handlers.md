# Handlers

Handlers are virtual methods in use by particles. Virtual methods are created to handle setters and getters in respect to edges and fields. There are four (4) types of handlers:

* **Get**: to retrieve an edge
* **Set**: to create an edge
* **Form**: not only to create an edge, but also its head node.
* **Has**: to check if such an edge does exist.

These adapters can be replaced, or more can be added using handler adapters via ```registerHandlerAdapter(string $handler_key, string $handler_class)``` function. For example:

```php
$this->registerHandlerAdapter(
            "form",
            \Pho\Kernel\Foundation\Handlers\Form::class);
```

## Get

##
