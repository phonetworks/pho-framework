<?php
namespace Pho\Framework;

class NotificationList implements \SplSubject
{

    protected $list = [];

    public function add(Notification $notification): void
    {
        $this->list[] = $notification;
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function toArray(): array
    {
        return array_map(function(Notification $notification) {
            return $notification->toArray();
        }, $this->notifications);
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
}