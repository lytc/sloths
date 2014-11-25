<?php

namespace Sloths\Observer;

trait ObserverTrait
{
    /**
     * @var Event[]
     */
    protected $eventListeners = [];

    /**
     * @param string|Event $event
     * @return string
     */
    protected function getEventName($event)
    {
        if ($event instanceof Event) {
            return $event->getName();
        }

        return $event;
    }

    /**
     * @param string|Event $event
     * @return bool
     */
    public function hasEventListener($event)
    {
        return isset($this->eventListeners[$this->getEventName($event)]);
    }

    /**
     * @param string|Event $event
     * @return null|Event
     */
    public function getEventListener($event)
    {
        $event = $this->getEventName($event);
        return $this->hasEventListener($event)? $this->eventListeners[$event] : null;
    }

    /**
     * @param array $events
     * @return $this
     */
    public function addEventListeners(array $events)
    {
        foreach ($events as $event => $callback) {
            $this->addEventListener($event, $callback);
        }

        return $this;
    }

    /**
     * @param string|Event $event
     * @param callable|Callback $callback
     * @param int $limit
     * @return $this
     */
    public function addEventListener($event, $callback, $limit = -1)
    {
        if (!$this->hasEventListener($event)) {
            $this->eventListeners[$event] = new Event($event);
        }

        $this->eventListeners[$event]->addCallback($callback, $limit);
        return $this;
    }

    /**
     * @param string $event
     * @param callable|Callback $callback
     * @return $this
     */
    public function addEventListenerOne($event, $callback)
    {
        return $this->addEventListener($event, $callback, 1);
    }

    /**
     * @param array $events
     * @return $this
     */
    public function addEventListenersOne(array $events)
    {
        foreach ($events as $event => $callback) {
            $this->addEventListenerOne($event, $callback);
        }

        return $this;
    }

    /**
     * @param string|Event $event
     * @param callable|Callback $callback
     * @return $this
     */
    public function removeEventListener($event, $callback = null)
    {
        if (!$callback) {
            unset($this->eventListeners[$this->getEventName($event)]);
            return $this;
        }

        if ($event = $this->getEventListener($event)) {
            $event->removeCallback($callback);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAllEventListeners()
    {
        $this->eventListeners = [];
        return $this;
    }

    /**
     * @param string|Event $event
     * @param array $args
     * @return bool|mixed|null
     */
    public function triggerEventListener($event, array $args = [])
    {
        $event = $this->getEventListener($event);

        if (!$event) {
            return;
        }

        return $event->call($args);
    }
}