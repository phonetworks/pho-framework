# Notifications

Take a look at 
* [AbstractNotification.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/AbstractNotification.php) class.
* and [ObjectOut/MentionNotification.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut/MentionNotification.php) class.

to see how notifications works.

Notifications are called by the ```execute()``` method of the edges. Example: [ObjectOut/Mention.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ObjectOut/Mention.php) and [ActorOut/Write.php](https://github.com/phonetworks/pho-framework/blob/master/src/Pho/Framework/ActorOut/Write.php)

!!! hyEdge!!!
