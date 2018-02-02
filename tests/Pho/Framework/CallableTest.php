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

use Pho\Lib\Graph\ID;
use Pho\Lib\Graph\Node;

/**
 * An edge example, with simple callable names.
 */
class Comment extends AbstractEdge // Actor -> Object (Comment)
{
    const TAIL_CALLABLE_LABEL = "comment";
    const TAIL_CALLABLE_LABELS = "comments";
    const HEAD_CALLABLE_LABEL = "comment";
    const HEAD_CALLABLE_LABELS = "comments";
}

class CommentPredicate extends Predicate {}

/**
 * A complicated edge example, with callable names in
 * camelized format.
 */
class Message extends AbstractEdge // Actor -> Actor (Message)
{
    const TAIL_CALLABLE_LABEL = "outgoingMessage";
    const TAIL_CALLABLE_LABELS = "outgoingMessages";
    const HEAD_CALLABLE_LABEL = "incomingMessage";
    const HEAD_CALLABLE_LABELS = "incomingMessages";
}

class MessagePredicate extends Predicate {}


class CallableTest extends \PHPUnit\Framework\TestCase 
{
    private $space, $actor, $random_id;

    public function setUp() {
        $this->space = new Space();
        $this->actor = new Actor($this->space);
        $this->random_id = ID::generate(new Node($this->space)); // can never collide, different entity types.
    }

    public function tearDown() {
        unset($this->space);
        unset($this->actor);
    }

    public function testSimpleCallable() {
        $obj = new Object($this->actor, $this->space);
        $comment = new Comment($this->actor, $obj);
        $this->actor->registerOutgoingEdges(Comment::class);
        $obj->registerIncomingEdges(Comment::class);
        $this->assertCount(1, $obj->getComments());
        $this->assertCount(1, $this->actor->getComments());
        $this->assertTrue($obj->hasComment($comment->id()));
        $this->assertTrue($this->actor->hasComment($comment->id()));
        $this->assertFalse($this->actor->hasComment($this->random_id));
        $this->assertFalse($obj->hasComment($this->random_id));
    }

    public function testSingularVsArray0() {
        $obj = new Object($this->actor, $this->space);
        $comment = new Comment($this->actor, $obj);
        $this->actor->registerOutgoingEdges(Comment::class);
        $obj->registerIncomingEdges(Comment::class);
        $this->assertSame($obj->getComments()[0]->id(), $obj->getComment()->id());
        $this->assertSame($obj->getComments()[0]->id(), $this->actor->getComment()->id());
    }

    public function testComplicatedCallable() {
        $actor = new Actor($this->space);
        
        $this->actor->registerOutgoingEdges(Message::class);
        $this->actor->registerIncomingEdges(Message::class);
        $actor->registerOutgoingEdges(Message::class);
        $actor->registerIncomingEdges(Message::class);

        $message = new Message($this->actor, $actor);
        //eval(\Psy\sh());
        $this->assertCount(1, $actor->getIncomingMessages());
        $this->assertCount(0, $actor->getOutgoingMessages());
        $this->assertCount(0, $this->actor->getIncomingMessages());
        $this->assertCount(1, $this->actor->getOutgoingMessages());
        $this->assertTrue($actor->hasIncomingMessage($message->id()));
        $this->assertTrue($this->actor->hasOutgoingMessage($message->id()));
        $this->assertFalse($this->actor->hasIncomingMessage($message->id()));
        $this->assertFalse($actor->hasOutgoingMessage($message->id()));

        $message = new Message($this->actor, $actor);
        $this->assertCount(2, $actor->getIncomingMessages());
        $this->assertCount(0, $actor->getOutgoingMessages());
        $this->assertCount(0, $this->actor->getIncomingMessages());
        $this->assertCount(2, $this->actor->getOutgoingMessages());
        $this->assertTrue($actor->hasIncomingMessage($message->id()));
        $this->assertTrue($this->actor->hasOutgoingMessage($message->id()));
        $this->assertFalse($this->actor->hasIncomingMessage($message->id()));
        $this->assertFalse($actor->hasOutgoingMessage($message->id()));

        $message = new Message($actor, $this->actor);
        $this->assertCount(2, $actor->getIncomingMessages());
        $this->assertCount(1, $actor->getOutgoingMessages());
        $this->assertCount(1, $this->actor->getIncomingMessages());
        $this->assertCount(2, $this->actor->getOutgoingMessages());
        $this->assertFalse($actor->hasIncomingMessage($message->id()));
        $this->assertFalse($this->actor->hasOutgoingMessage($message->id()));
        $this->assertTrue($this->actor->hasIncomingMessage($message->id()));
        $this->assertTrue($actor->hasOutgoingMessage($message->id()));
    }
    
}