# CHANGELOG

This changelog references the relevant changes (bug and security fixes) introduced with version 8.0 and beyond.

To get the diff for a specific change, go to https://github.com/phonetworks/pho-framework/commit/XXX where XXX is the change hash.

To get the diff between two versions, go to https://github.com/phonetworks/pho-framework/compare/v8.0.0...v7.5.0

## 7.5 to 8.0

* With particles, the ```registerOutgoingEdgeClass``` is gone, and is now replaced with ```registerOutgoingEdge```.
* The {ParticleName}/{EdgeName}Out folder structure to define edges is now replaced with the explicit "registerOutgoingEdge" method only.
* Particle's ```registerHandlerAdapter(string $class)``` method is replaced with ```registerHandler(string $class)```.
* Two new signals; outgoing_edge.registered and edge.registered
* ParticleTrait \_\_construct (which was imported by particles via "particleConstructor" name) is now named as: "initializeParticle".
* The internals of OutgoingEdgeLoader simplified.
* Documentation (under docs/) updated to reflect the latest API changes.
* Unit tests updated to reflect the latest changes in the API.

## 8.0 to 8.1

* Support for autoRegisterOutgoingEdges back. The directory structure {particle_name}Out/\* would be parsed for outgoing edges.


## 8.1 to 8.2 
* Introduced quietSet to set up node edges at construction without triggering an event each time.

## 8.2 to 8.3
* Edges now can also have fields.

## 8.3 to 8.4
* exportCargo method added to particles.

## 8.4 to 8.5
* Switched to pho-lib-graph's new ID
* node.added signals are now thrown once the particle is fully initialized.

## 8.5 to 8.6
* Added new format constraints; e.g format="numeric",  "ip", "email", "url", "creditCard"

## 8.6 to 8.7
* added sha1 directive (for passwords) besides archaic md5

## 8.x to 9.0
* Standardized getter and setter cargos. Fields are stored upper-camelized while all others are stored camelized.

## 9.x to 9.1
* Tail/Head callables introduced. Now you can call directly edges via getters (useful for examples like Message, Comment)

## 9.1 to 9.2
* Important bugfixes

## 9.2 to 9.3
* Singular picking enabled. getComments()[0] = getComment()

## 9.3 to 9.4
* regex delimiters are now a must

## 9.4 to 10.0
* removed sabre-event dependency
* no more observer pattern which was complicating.

## 10.0 to 11.0
* Object to Obj
* moved ID headers off of pho-lib-graph towars this framework

## 11.0 to 11.1
* backwards compatibility for the Object to Obj switch

## 11.1 to 11.2
* added long form formative edges. E.g $node->post also $node->postBlog

## 11.2 to 11.3
* added SubscribeNotification

## 11.3 to 11.4
* added notifications.read signal

## 11.4 to 11.5
* pho-lib-graph 9.0 update to DHT support