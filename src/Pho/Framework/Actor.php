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

/**
 * The Actor Particle
 * 
 * Actors have three outgoing edges:
 * * {@link Actor\Reads}
 * * {@link Actor\Writes}
 * * {@link Actor\Subscribes}
 * 
 * @method AbstractEdge reads(ParticleInterface $particle)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Actor extends \Pho\Lib\Graph\Node implements ParticleInterface, \SplObserver, \SplSubject
{

    use ParticleTrait {
        ParticleTrait::__construct as particleConstructor;
    }

    /**
     * Current context that this actor is in.
     *
     * @var ContextInterface
     */
    protected $current_context;

    /**
     * Notifications
     *
     * @var NotificationList
     */
    protected $notifications;

    public function __construct(ContextInterface $context) 
    {
        parent::__construct($context);
        $this->creator = $this;
        $this->creator_id = (string) $this->id();
        $this->notifications = new NotificationList($this);
        $this->enter($context);
        $this->particleConstructor();
    }

    /**
     * Puts the Actor into a context
     * 
     * This is importnat because All particles formed by the Actor 
     * will be associated with their current context.
     * 
     * @see Actor:cwd for UNIX-style alias.
     * 
     * @param ContextInterface $context
     * 
     * @return void
     */
    public function enter(ContextInterface $context): void 
    {
        $this->current_context = $context;
    }

    /**
     * Alias to enter()
     * 
     * This is a UNIX alias to the ```enter()``` method.
     * 
     * @see Actor::enter 
     * 
     * @param ContextInterface $context
     * 
     * @return void
     */
    public function cwd(ContextInterface $context): void 
    {
        $this->enter($context);
    }

    /**
     * Returns which context the Actor is currently operating
     * 
     * This is importnat because All particles formed by the Actor 
     * will be associated with their current context.
     * 
     * @see Actor:pwd for UNIX-style alias.
     * 
     * @return ContextInterface Current context where the Actor is operating. 
     */
    public function where(): ContextInterface
    {
        if(is_null($this->current_context)) {
            $this->enter($this->context);
        }
        return $this->current_context;
    }

    /**
     * Alias to where()
     * 
     * This is a UNIX alias to the ```where()``` method.
     * 
     * @see Actor::where 
     * 
     * @return ContextInterface Current context where the Actor is operating. 
     */
    public function pwd(): ContextInterface 
    {
        return $this->where();
    }



    public function update(\SplSubject $subject): void
    {
        if($subject instanceof NotificationList) {
            $this->observeNotificationListUpdate($subject);
            return;
        }
        parent::update($subject);
    }

    protected function observeNotificationListUpdate(AbstractNotification $notification): void
    {

    }

    /**
     * Gives access to the actor's notifications list
     *
     * @return NotificationList
     */
    public function notifications(): NotificationList
    {
        return $this->notifications;
    }

}