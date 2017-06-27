<?php
namespace Pho\Framework;

class NotificationList implements \SplSubject
{

    protected $list = [];
    protected $observers = [];

    public function __construct(Actor $owner)
    {
        $this->attach($owner);
    }

    public function add(AbstractNotification $notification): void
    {
        $this->list[] = $notification;
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function toArray(): array
    {
        return array_map(function(AbstractNotification $notification) {
            return $notification->toArray();
        }, $this->list);
    }

    // also deletes
    public function read(int $limit = 5): array
    {
        $read = [];
        for($i=0;$i<$limit;$i++) {
            $read[] = array_pop($this->list);
        }
        return $read;
    }



    /**********************************************
     * The rest are \SplSubject functions.
     *********************************************/

    /**
     * Adds a new observer to the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function attach(\SplObserver $observer): void 
    {
        $this->observers[] = $observer;
    }
    
    /**
     * Removes an observer from the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function detach(\SplObserver $observer): void 
    {
        $key = array_search($observer, $this->observers, true);
        if($key) {
            unset($this->observers[$key]);
        }
    }

    /**
     * Notifies observers about deletion
     * 
     * @return void
     */
    public function notify(): void
    {
        foreach ($this->observers as $value) {
            $value->update($this);
        }
    }
}