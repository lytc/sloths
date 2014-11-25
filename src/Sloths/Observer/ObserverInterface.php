<?php

namespace Sloths\Observer;

interface ObserverInterface
{
    /**
     * @param string|Event $event
     * @return bool
     */
    public function hasEventListener($event);

    /**
     * @param string|Event $event
     * @return null|Event
     */
    public function getEventListener($event);

    /**
     * @param string|Event $event
     * @param callable|Callback $callback
     * @param int $limit
     * @return $this
     */
    public function addEventListener($event, $callback, $limit = -1);

    /**
     * @param array $events
     * @return $this
     */
    public function addEventListeners(array $events);

    /**
     * @param string $event
     * @param callable|Callback $callback
     * @return $this
     */
    public function addEventListenerOne($event, $callback);

    /**
     * @param array $events
     * @return $this
     */
    public function addEventListenersOne(array $events);

    /**
     * @param string|Event $event
     * @param callable|Callback $callback
     * @return $this
     */
    public function removeEventListener($event, $callback = null);

    /**
     * @return $this
     */
    public function removeAllEventListeners();

    /**
     * @param string|Event $event
     * @param array $args
     * @return bool|mixed|null
     */
    public function triggerEventListener($event, array $args = []);
}