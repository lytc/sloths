<?php

namespace Sloths\Observer;

trait ObserverTrait
{
    /**
     * @var array
     */
    protected $observerListeners = [];

    /**
     * @var array
     */
    protected $listeningTo = [];


    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->observerListeners;
    }

    /**
     * @param string $eventName
     * @return array
     */
    public function getListener($eventName)
    {
        return isset($this->observerListeners[$eventName])? $this->observerListeners[$eventName] : [];
    }

    /**
     * @param string [$eventName]
     * @param callable $callback
     * @return bool
     */
    public function hasListener($eventName = null, callable $callback = null)
    {
        if (!$eventName) {
            return !!$this->observerListeners;
        }

        if (!isset($this->observerListeners[$eventName])) {
            return false;
        }

        if ($callback) {
            return $this->observerListeners[$eventName]->contains($callback);
        }

        return !!count($this->getListener($eventName));
    }

    /**
     * @param string $eventName
     * @param callable $callback
     * @param int [$limitCall=-1]
     * @return $this
     */
    public function addListener($eventName, \Closure $callback, $limitCall = -1)
    {
        if (!isset($this->observerListeners[$eventName])) {
            $this->observerListeners[$eventName] = new \SplObjectStorage();
        }

        $this->observerListeners[$eventName]->attach($callback, $limitCall);
        return $this;
    }

    /**
     * @param string $eventName
     * @param callable $callback
     * @return $this
     */
    public function addListenerOne($eventName, callable $callback)
    {
        return $this->addListener($eventName, $callback, 1);
    }

    /**
     * @param string [$eventName]
     * @param callable $callback
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function removeListener($eventName = null, callable $callback = null)
    {
        if (!$eventName) {
            $this->observerListeners = [];
            return $this;
        }

        if (!isset($this->observerListeners[$eventName])) {
            throw new \InvalidArgumentException(sprintf('%s: Event not found', $eventName));
        }

        if (!$callback) {
            $this->observerListeners[$eventName]->removeAll($this->observerListeners[$eventName]);
            return $this;
        }

        $this->observerListeners[$eventName]->detach($callback);
        return $this;
    }

    /**
     * @param Observer $target
     * @param string $eventName
     * @param callable $callback
     * @param int [$limitCall=-1]
     * @return $this
     */
    public function listenTo(Observer $target, $eventName, callable $callback, $limitCall = -1)
    {
        $that = $this;
        $bindCallback = $callback->bindTo($this, $this);
        $listenCallback = function() use ($that, $bindCallback, &$limitCall, $target, $eventName, $callback) {
            $limitCall--;
            if ($limitCall == 0) {
                $that->stopListening($target, $eventName, $callback);
            }
            return call_user_func_array($bindCallback, func_get_args());
        };

        $this->listeningTo[] = [
            'target'            => $target,
            'eventName'         => $eventName,
            'callback'          => $callback,
            'listenCallback'    => $listenCallback
        ];

        $target->addListener($eventName, $listenCallback);

        return $this;
    }

    /**
     * @param Observer $target
     * @param string $eventName
     * @param callable $callback
     * @return $this
     */
    public function listenOneTo(Observer $target, $eventName, callable $callback)
    {
        return $this->listenTo($target, $eventName, $callback, 1);
    }

    /**
     * @param Observer $target
     * @param string [$eventName]
     * @param callable $callback
     * @return $this
     */
    public function stopListening(Observer $target = null, $eventName = null, callable $callback = null)
    {
        if (!$target) {
            foreach ($this->listeningTo as $info) {
                $info['target']->removeListener($info['eventName'], $info['listenCallback']);
            }
            $this->listeningTo = [];
            return $this;
        }

        $matched = function($info) use ($target, $eventName, $callback) {
            $result = $target == $info['target'];
            !$eventName || $result = ($result && ($eventName == $info['eventName']));
            !$callback || $result = ($result && ($callback == $info['callback']));

            return $result;
        };

        foreach ($this->listeningTo as $index => $info) {
            if (!$matched($info)) {
                continue;
            }
            $info['target']->removeListener($info['eventName'], $info['listenCallback']);
            unset($this->listeningTo[$index]);
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param array $args
     * @return $this
     */
    public function notify($eventName, array $args = [])
    {
        if (!isset($this->observerListeners[$eventName])) {
            return $this;
        }

        $listeners = $this->observerListeners[$eventName];

        foreach ($listeners as $callback) {
            $listeners->setInfo($listeners[$callback] - 1);
            if ($listeners[$callback] == 0) {
                $this->removeListener($eventName, $callback);
            }

            $callback = $callback->bindTo($this, $this);
            if (false === call_user_func_array($callback, $args)) {
                return false;
            }
        }

        return $this;
    }
}