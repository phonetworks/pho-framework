# Signals

* **notification.received(AbstractNotification $notification)**: called when the actor received a notification. This may alternatively be achieved by overriding the ```observeNotificationListUpdate(AbstractNotification $notification)``` function.

* **incoming_edge.registered(string $class)**: called by particles when registering incoming edges. This function may be used extra edges easily and independently, without extending the constructor itself.

* **outgoing_edge.registered(string $class)**: called by particles when registering outgoing edges. This function may be used extra edges easily and independently, without extending the constructor itself.

* **edge.registered(string $direction, string $class)**: called by particles when registering edges. This function may be used extra edges easily and independently, without extending the constructor itself.
