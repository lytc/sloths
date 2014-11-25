<?php

namespace Sloths\Session\Adapter;

interface AdapterInterface
{
    /**
     * @return bool
     */
    public function isStarted();

    /**
     * @return $this
     */
    public function start();

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param bool $deleteOldSession
     * @return $this
     */
    public function regenerateId($deleteOldSession = false);

    /**
     * @return $this
     */
    public function save();

    /**
     * @return $this
     */
    public function destroy();

    /**
     * @return \Sloths\Session\Container
     */
    public function getContainer();
}