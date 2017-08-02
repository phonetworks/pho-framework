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