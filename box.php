<?php

/**
 * Box
 */
class Box
{
    /**
     * @var array
     */
    protected $container;

    /**
     * Construct
     *
     * @param array $list
     */
    public function __construct(array $list = null)
    {
        if ($list)
        {
            foreach ($list as $key => $value)
            {
                if ($value instanceof \Closure) $this->bind($key, $value);
            }
        }
    }

    /**
     * Map a factory against a name
     *
     * @param string   $name
     * @param callable $factory
     */
    public function bind($name, \Closure $factory)
    {
        $this->container[$name] = $factory;
    }

    /**
     * Looks up and executes the factory for the specified name
     *
     * @param  string $name
     *
     * @return mixed
     */
    public function make($name)
    {
        return isset($this->container[$name]) ? $this->container[$name]() : null;
    }

    /**
     * Wraps a factory so it's only invoked once, and caches its value
     *
     * @param callable $factory
     *
     * @return callable
     */
    public function shared(\Closure $factory)
    {
        $instance = null;

        return function () use (&$instance, $factory)
        {
            if ($instance === null) $instance = $factory();

            return $instance;
        };
    }

    /**
     * Creates a factory that returns the passed value in $any
     *
     * @param  mixed $any
     *
     * @return callable
     */
    public function value($any)
    {
        return function () use ($any)
        {
            return $any;
        };
    }

    /**
     * Wrap a \Closure, providing dependencies during call-time
     *
     * @param  array    $dependencies
     * @param  callable $function
     *
     * @return callable
     */
    public function provide(array $dependencies, \Closure $function)
    {
        return function () use ($dependencies, $function)
        {
            $arguments = [];

            foreach ($dependencies as $dependency)
            {
                $arguments[] = $this->make($dependency);
            }

            return $function(...$arguments); //return call_user_func_array($function, $arguments);
        };
    }

    /**
     * Map a shared factory against a name
     *
     * @param string   $name
     * @param callable $factory
     */
    public function bindShared($name, \Closure $factory)
    {
        $this->bind($name, $this->shared($factory));
    }

    /**
     * Maps a string against a name
     *
     * @param string $name
     * @param string $any
     */
    public function bindValue($name, $any)
    {
        $this->bind($name, $this->value($any));
    }
}
