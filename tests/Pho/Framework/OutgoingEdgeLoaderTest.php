<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework {

    class SingleFieldObjectWithDefaultValue extends Obj
    {
        public function __construct(Actor $creator, ContextInterface $context, string $first_field = "emre")
        {
            parent::__construct($creator, $context);
        }
    }

    class SingleFieldObject extends Obj
    {
        protected $props = array();
        public function __construct(Actor $creator, ContextInterface $context, string $first_field)
        {
            parent::__construct($creator, $context);
            $this->props[] = $first_field;
        }
    }

    class TwoFieldsObject extends SingleFieldObject
    {
        public function __construct(Actor $creator, ContextInterface $context, string $first_field, int $second_field = 1)
        {
            parent::__construct($creator, $context, $first_field);
            $this->props[] = $second_field;
        }
    }

    class ThreeFieldsObjectWithBool extends TwoFieldsObject
    {
        public function __construct(Actor $creator, ContextInterface $context, string $first_field, int $second_field = 1, bool $third_field = false)
        {
            parent::__construct($creator, $context, $first_field, $second_field);
            $this->props[] = $third_field;
        }
    }

    class ThreeFieldsObjectWithNullable extends TwoFieldsObject
    {
        public function __construct(Actor $creator, ContextInterface $context, string $first_field, int $second_field = 1, ?bool $third_field = false)
        {
            parent::__construct($creator, $context, $first_field, $second_field);
            $this->props[] = $third_field;
        }
    }

    class FourFieldsObjectWithNullable extends ThreeFieldsObjectWithNullable
    {
        public function __construct(Actor $creator, ContextInterface $context, string $first_field, int $second_field = 1, ?bool $third_field = false, ?string $forth_field = null)
        {
            parent::__construct($creator, $context, $first_field, $second_field, $third_field);
            $this->props[] = $forth_field;
        }
    }


    class FieldsTest extends \PHPUnit\Framework\TestCase 
    {
        private $space, $actor;

        public function setUp() {
            $this->space = new Space();
            $this->actor = new Actor($this->space);
        }

        public function tearDown() {
            unset($this->space);
            unset($this->actor);
        }

        public function testSingleFieldObjectWithDefaultValue() {
            $this->actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post0::class);
            $ref = new \ReflectionObject($this->actor);
            $prop = $ref->getProperty("handler");
            $prop->setAccessible(true);
            $handler = $prop->getValue($this->actor);
            $formativePatterns = $handler->cargo_out->formative_patterns;
            /*
            [
                "post1SingleFieldObject" => [
                  "Pho\Framework\SingleFieldObject" => "string",
                ],
                "post1" => [
                  "Pho\Framework\Obj" => ":::",
                  "Pho\Framework\SingleFieldObject" => "string",
                ],
              ]
              */

              /*
            foreach($formativePatterns as $verb => $dual) {
                foreach($dual as $class=>$pattern) {

                }
            }
            */
            $this->assertArrayHasKey("post0SingleFieldObjectWithDefaultValue", $formativePatterns);
            $this->assertArrayHasKey("post0", $formativePatterns);

            $this->assertArrayHasKey(\Pho\Framework\SingleFieldObjectWithDefaultValue::class, $formativePatterns["post0SingleFieldObjectWithDefaultValue"]);
            $this->assertContains("(string)?", $formativePatterns["post0SingleFieldObjectWithDefaultValue"]);
        }

        public function testSingleFieldObject() {
            $this->actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post1::class);
            $ref = new \ReflectionObject($this->actor);
            $prop = $ref->getProperty("handler");
            $prop->setAccessible(true);
            $handler = $prop->getValue($this->actor);
            $formativePatterns = $handler->cargo_out->formative_patterns;
            /*
            [
                "post1SingleFieldObject" => [
                  "Pho\Framework\SingleFieldObject" => "string",
                ],
                "post1" => [
                  "Pho\Framework\Obj" => ":::",
                  "Pho\Framework\SingleFieldObject" => "string",
                ],
              ]
              */

              /*
            foreach($formativePatterns as $verb => $dual) {
                foreach($dual as $class=>$pattern) {

                }
            }
            */
            $this->assertArrayHasKey("post1SingleFieldObject", $formativePatterns);
            $this->assertArrayHasKey("post1", $formativePatterns);

            $this->assertArrayHasKey(\Pho\Framework\SingleFieldObject::class, $formativePatterns["post1SingleFieldObject"]);
            $this->assertContains("string", $formativePatterns["post1SingleFieldObject"]);
        }

        public function testTwoFieldsObject() {
            $this->actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post2::class);
            $ref = new \ReflectionObject($this->actor);
            $prop = $ref->getProperty("handler");
            $prop->setAccessible(true);
            $handler = $prop->getValue($this->actor);
            $formativePatterns = $handler->cargo_out->formative_patterns;
            $this->assertArrayHasKey("post2TwoFieldsObject", $formativePatterns);
            $this->assertArrayHasKey("post2", $formativePatterns);
            
            $this->assertArrayHasKey(\Pho\Framework\TwoFieldsObject::class, $formativePatterns["post2TwoFieldsObject"]);
            //$this->assertContains("string(:::int)?(:::bool)?", $formativePatterns["post2TwoFieldsObject"]); // because second has a default value
            $this->assertContains("string(:::int)?", $formativePatterns["post2TwoFieldsObject"]); // because second has a default value
            /*
                    [
            "post2TwoFieldsObject" => [
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
            "post2" => [
            "Pho\Framework\Obj" => ":::",
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
        ]
            */
        }

        public function testThreeFieldsObjectWithBool() {
            $this->actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post3v1::class);
            $ref = new \ReflectionObject($this->actor);
            $prop = $ref->getProperty("handler");
            $prop->setAccessible(true);
            $handler = $prop->getValue($this->actor);
            
            $formativePatterns = $handler->cargo_out->formative_patterns;
            ///eval(\Psy\sh());
            $this->assertArrayHasKey("post3V1ThreeFieldsObjectWithBool", $formativePatterns);
            $this->assertArrayHasKey("post3V1", $formativePatterns);
            //eval(\Psy\sh());
            
            $this->assertArrayHasKey(\Pho\Framework\ThreeFieldsObjectWithBool::class, $formativePatterns["post3V1ThreeFieldsObjectWithBool"]);
            $this->assertContains("string(:::int)?(:::bool)?", $formativePatterns["post3V1ThreeFieldsObjectWithBool"]); // because second has a default value
            
            
            /*
                    [
            "post2TwoFieldsObject" => [
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
            "post2" => [
            "Pho\Framework\Obj" => ":::",
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
        ]
            */
        }

        public function testThreeFieldsObjectWithNullable() {
            $this->actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post3v2::class);
            $ref = new \ReflectionObject($this->actor);
            $prop = $ref->getProperty("handler");
            $prop->setAccessible(true);
            $handler = $prop->getValue($this->actor);
            
            $formativePatterns = $handler->cargo_out->formative_patterns;
            ///eval(\Psy\sh());
            $this->assertArrayHasKey("post3V2ThreeFieldsObjectWithNullable", $formativePatterns);
            $this->assertArrayHasKey("post3V2", $formativePatterns);
            //eval(\Psy\sh());
            
            $this->assertArrayHasKey(\Pho\Framework\ThreeFieldsObjectWithNullable::class, $formativePatterns["post3V2ThreeFieldsObjectWithNullable"]);
            $this->assertContains("string(:::int)?(:::bool)?", $formativePatterns["post3V2ThreeFieldsObjectWithNullable"]); // because second has a default value
            
            
            /*
                    [
            "post2TwoFieldsObject" => [
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
            "post2" => [
            "Pho\Framework\Obj" => ":::",
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
        ]
            */
        }


        public function testFourFieldsObject() {
            $this->actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post4::class);
            $ref = new \ReflectionObject($this->actor);
            $prop = $ref->getProperty("handler");
            $prop->setAccessible(true);
            $handler = $prop->getValue($this->actor);
            
            $formativePatterns = $handler->cargo_out->formative_patterns;
            ///eval(\Psy\sh());
            $this->assertArrayHasKey("post4FourFieldsObjectWithNullable", $formativePatterns);
            $this->assertArrayHasKey("post4", $formativePatterns);
            //eval(\Psy\sh());
            
            $this->assertArrayHasKey(\Pho\Framework\FourFieldsObjectWithNullable::class, $formativePatterns["post4FourFieldsObjectWithNullable"]);
            $this->assertContains("string(:::int)?(:::bool)?(:::string)?", $formativePatterns["post4FourFieldsObjectWithNullable"]); // because second has a default value
            
            
            /*
                    [
            "post2TwoFieldsObject" => [
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
            "post2" => [
            "Pho\Framework\Obj" => ":::",
            "Pho\Framework\TwoFieldsObject" => "string(:::int:::)?",
            ],
        ]
            */
        }
    }

};


namespace Pho\Framework\ActorOut {
    class Post0 extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\SingleFieldObjectWithDefaultValue::class]; 
    }
    class Post0Predicate extends WritePredicate {
        const T_FORMATIVE = true;
    }

    class Post1 extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\SingleFieldObject::class]; 
    }
    class Post1Predicate extends WritePredicate {
        const T_FORMATIVE = true;
    }

    class Post2 extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\TwoFieldsObject::class]; 
    }
    class Post2Predicate extends WritePredicate {
        const T_FORMATIVE = true;
    }

    class Post3v1 extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\ThreeFieldsObjectWithBool::class]; 
    }
    class Post3v1Predicate extends WritePredicate {
        const T_FORMATIVE = true;
    }

    class Post3v2 extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\ThreeFieldsObjectWithNullable::class]; 
    }
    class Post3v2Predicate extends WritePredicate {
        const T_FORMATIVE = true;
    }

    class Post4 extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\FourFieldsObjectWithNullable::class]; 
    }
    class Post4Predicate extends WritePredicate {
        const T_FORMATIVE = true;
    }

};