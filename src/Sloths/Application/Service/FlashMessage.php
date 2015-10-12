<?php

namespace Sloths\Application\Service;

class FlashMessage implements \Countable, \IteratorAggregate, ServiceInterface
{
    use ServiceTrait;

    /**
     *
     */
    const SUCCESS   = 'success';
    /**
     *
     */
    const INFO      = 'info';
    /**
     *
     */
    const WARNING   = 'warning';
    /**
     *
     */
    const DANGER    = 'danger';
    /**
     *
     */
    const ERROR     = 'error';

    /**
     * @var
     */
    protected $flashSession;

    /**
     * @var string
     */
    protected $paramName = '__message__';

    /**
     * @var array
     */
    protected $currentMessages = [];

    /**
     * @var \ArrayObject
     */
    protected $nextMessages;

    /**
     * @return $this
     */
    public function boot()
    {
        $flash = $this->getApplication()->getServiceManager()->get('session')->flash();

        $this->currentMessages = $flash->get('messages')?: [];
        $this->nextMessages = new \ArrayObject();
        $flash->set('messages', $this->nextMessages);
    }

    /**
     * @return \ArrayObject
     */
    public function getNextMessages()
    {
        return $this->nextMessages;
    }

    /**
     * @param $type
     * @param $text
     * @return $this
     */
    public function add($type, $text)
    {
        $this->nextMessages[] = [
            'type' => $type,
            'text' => $text
        ];

        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function success($text)
    {
        return $this->add(self::SUCCESS, $text);
    }

    /**
     * @param $text
     * @return $this
     */
    public function info($text)
    {
        return $this->add(self::INFO, $text);
    }

    /**
     * @param $text
     * @return $this
     */
    public function warning($text)
    {
        return $this->add(self::WARNING, $text);
    }

    /**
     * @param $text
     * @return $this
     */
    public function danger($text)
    {
        return $this->add(self::DANGER, $text);
    }

    /**
     * @param $text
     * @return $this
     */
    public function error($text)
    {
        return $this->add(self::ERROR, $text);
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->currentMessages;
    }

    /**
     * @param $type
     * @return array
     */
    public function get($type)
    {
        $result = [];

        foreach ($this->getAll() as $messages) {
            if ($messages['type'] == $type) {
                $result[] = $messages['text'];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getSuccesses()
    {
        return $this->get(self::SUCCESS);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->get(self::INFO);
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->get(self::WARNING);
    }

    /**
     * @return array
     */
    public function getDangers()
    {
        return $this->get(self::DANGER);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->get(self::ERROR);
    }

    public function count()
    {
        return count($this->getAll());
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getAll()?: []);
    }
}