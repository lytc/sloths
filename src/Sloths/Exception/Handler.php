<?php

namespace Sloths\Exception;

class Handler
{
    /**
     * @var
     */
    protected $previousHandler;
    /**
     * @var bool
     */

    protected $registered = false;
    /**
     * @var array
     */

    protected $handlers = [];
    /**
     * @var
     */

    protected static $instance;

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param bool $handleError
     */
    public function __construct($handleError = true)
    {
        if ($handleError) {
            set_error_handler(function($level, $message, $file, $line) {
                throw new \ErrorException($message, 0, $level, $file, $line);
            });
        }

        $this->register();
    }

    /**
     * @return $this
     */
    public function register()
    {
        if (!$this->registered) {
            $this->previousHandler = set_exception_handler([$this, 'handle']);
            $this->registered = true;
        }

        return $this;

    }

    /**
     * @param \Exception $e
     */
    public function handle(\Exception $e)
    {
        $exceptionClassName = get_class($e);

        if (isset($this->handlers[$exceptionClassName])) {
            foreach ($this->handlers[$exceptionClassName] as $handler) {
                if (false !== $handler($e)) {
                    return;
                }
            }
        }

        foreach ($this->handlers as $key => $handler) {
            if (is_string($key)) {
                continue;
            }

            $reflection = new \ReflectionFunction($handler);
            $class = $reflection->getParameters()[0]->getClass()->getName();

            if ($e instanceof $class) {
                if (false !== $handler($e)) {
                    return;
                }
            }
        }
    }

    /**
     * @param $exceptionClassName
     * @param callable $handler
     * @return $this
     */
    public function add($exceptionClassName, \Closure $handler = null)
    {
        if (!$handler) {
            $handler = $exceptionClassName;
            $exceptionClassName = null;
        }

        if (!$exceptionClassName) {
            $this->handlers[] = $handler;
        } else {
            if (!isset($this->handlers[$exceptionClassName])) {
                $this->handlers[$exceptionClassName] = [];
            }

            $this->handlers[$exceptionClassName][] = $handler;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function restore()
    {
        if ($this->previousHandler) {
            set_exception_handler($this->previousHandler);
            $this->previousHandler = null;
            $this->registered = false;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->handlers = [];
        return $this;
    }
}