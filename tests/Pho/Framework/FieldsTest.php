<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework;

class DirectedTestObject extends Object
{
    const FIELDS = [
        "my_field" => [
            "constraints" => [
                "minLength" => null,
                "maxLength" => null,
                "uuid" => null,
                "regex" => null,
                "greaterThan" => null,
                "lessThan" => null,
            ],
            "directives" => [
                "md5" => true,
                "now" => false,
                "default" => "",
            ]
        ]
    ];
}

class ConstraintedTestObject extends Object
{
    const FIELDS = [
        "my_field" => [
            "constraints" => [
                "minLength" => 6,
                "maxLength" => null,
                "uuid" => null,
                "regex" => null,
                "greaterThan" => null,
                "lessThan" => null,
            ],
            "directives" => [
                "md5" => false,
                "now" => false,
                "default" => "",
            ]
        ]
    ];
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

    public function testDirectedObject() {
        
        $obj = new DirectedTestObject($this->actor, $this->space);
        $obj->setMyField("emre");
        //$node_expected_to_be_identical = $this->space->get($node->id());
        $this->assertEquals(md5("emre"), $obj->getMyField());
    }

    public function testConstraintedObjectWithPositive() {
        $obj = new ConstraintedTestObject($this->actor, $this->space);
        $obj->setMyField("123456");
        $this->assertEquals("123456", $obj->getMyField());
    }

    public function testConstraintedObjectWithNegative() {
        $obj = new ConstraintedTestObject($this->actor, $this->space);
        $this->expectException("\InvalidArgumentException");
        $obj->setMyField("1234");
    }

    public function testCamelCaseVariableWithUuidConstraint() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "myField" => [
                    "constraints" => [
                        "uuid" => true
                    ]
                ]
            ];
        };
        $uuid = \Pho\Lib\Graph\ID::generate($obj);
        $obj->setMyField($uuid);
        $this->assertEquals($uuid, $obj->getMyField());
    }

    public function testUuidConstraintWithNegative() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "myField" => [
                    "constraints" => [
                        "id" => true
                    ]
                ]
            ];
        };
        $this->expectException(\InvalidArgumentException::class);
        $obj->setMyField("not_uuid");
    }

    public function testRegexConstraintWithPositive() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "MyField" => [
                    "constraints" => [
                        "regex" => "^A[0-9]+1\$"
                    ]
                ]
            ];
        };
        $field_val = "A883841";
        $obj->setMyField($field_val);
        $this->assertEquals($field_val, $obj->getMyField());
    }

    public function testRegexConstraintWithNegative() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "MyField" => [
                    "constraints" => [
                        "regex" => "^A[0-9]+1\$"
                    ]
                ]
            ];
        };
        $this->expectException("\InvalidArgumentException");
        $obj->setMyField("will_not_match_regexp");
    }

    public function testFormatConstraintsNegative() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "MyField" => [
                    "constraints" => [
                        "format" => "email"
                    ]
                ]
            ];
        };
        $this->expectException("\InvalidArgumentException");
        $obj->setMyField("not_an_email!");
    }

    public function testDateConstraints() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "MyField" => [
                    "constraints" => [
                        "dateBefore" => "01/20/2018",
                        "dateAfter" => "01/15/2018",
                    ]
                ]
            ];
        };
        //eval(\Psy\sh());
        $this->expectException("\InvalidArgumentException");
        $obj->setMyField("01/20/2018");
    }

    public function testDateConstraintsPositive() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "MyField" => [
                    "constraints" => [
                        "dateBefore" => "01/20/2018",
                        "dateAfter" => "01/15/2018",
                    ]
                ]
            ];
        };
        $obj->setMyField("01/18/2018");
        $this->assertEquals("01/18/2018", $obj->getMyField());
    }

    public function testFormatConstraintsPositive() {
        $obj = new class($this->actor, $this->space) extends Object {
            const FIELDS = [
                "MyField" => [
                    "constraints" => [
                        "format" => "email"
                    ]
                ]
            ];
        };
        $email = "emre@groups-inc.com";
        $obj->setMyField($email);
        $this->assertEquals($email, $obj->getMyField());
    }

    public function testJsonFields() {
        $obj = new class($this->actor, $this->space)  extends Object {
            const FIELDS = '{"MyField":{"constraints":{"regex":"^A[0-9]+1$"}}}';
        };
        $field_val = "A883841";
        $obj->setMyField($field_val);
        $this->assertEquals($field_val, $obj->getMyField());
    }

    public function testEdgeFields() {
        $ref = 0;
        $another_actor = new Actor($this->space);
        $obj = new class($this->actor, $another_actor)  extends AbstractEdge {
            const FIELDS = '{"my_field":{"constraints":{"regex":"^A[0-9]+1$"}}}';
        };
        $obj->on("modified", function() use(&$ref) {
            $ref++;
        });
        $field_val1 = "A883841";
        $field_val2 = "A4896541";
        $obj->setMyField($field_val1);
        $this->assertEquals($field_val1, $obj->getMyField());
        $this->assertEquals(1, $ref); // signal
        $obj->setMyField($field_val2, true);
        $this->assertEquals($field_val2, $obj->getMyField());
        $this->assertEquals(1, $ref); // no signal
    }

    public function testEdgeFieldsAutoPilot() {
        $field_val1 = "A883841";
        $past_time = 1502224636;
        $another_actor = new Actor($this->space);
        $obj = new class($this->actor, $another_actor, null, $field_val1)  extends AbstractEdge {
            const FIELDS = '{"my_field":{"constraints":{"regex":"^A[0-9]+1$"}},"created_at":{"directives":{"now":true
}},"with_default":{"directives":{"default":"defne"}}}'; 
            // '{"my_field":{"constraints":{"regex":"^A[0-9]+1$"}},"created_at":{"directives":{"now":true}}}';
        };
        //eval(\Psy\sh());
        $this->assertGreaterThan($past_time, $obj->getCreatedAt());
        $this->assertEquals("defne", $obj->getWithDefault());
    }

}