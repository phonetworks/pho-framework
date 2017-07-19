# Signals

* **notification.received**: called when the actor received a notification. This may alternatively be achieved by overriding the ```observeNotificationListUpdate(AbstractNotification $notification)``` function.

* **incoming_edge.registered**: called by particles when registering incoming edges. This function may be used extra edges easily and independently, without extending the constructor itself.
