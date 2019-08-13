<?php
namespace Pho\Framework;

class NotificationList 
{

    protected $owner;
    protected $list = [];

    public function __construct(Actor $owner, array $data = [])
    {
        $this->owner = $owner;
        $this->import($data);
    }

    protected function import(array $data): void
    {
        foreach($data as $d) {
            $this->add($d);
        }
    }

    public function add(AbstractNotification $notification): void
    {
        $this->list[] = $notification;
        $this->owner->emit("notification.received", [$notification]);
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
        $limit = ( $this->count() < $limit ) ? $this->count() : $limit;
        for($i=0;$i<$limit;$i++) {
            $read[] = array_pop($this->list);
        }
        if(count($read)>0) {
            $this->owner->emit("notifications.read", [count($read)]);
        }
        return $read;
    }
}