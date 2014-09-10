<?php

/**
 * Box
 *
 * @see https://github.com/noodlehaus/ioc/blob/master/ioc.php
 */
class Box
{
    /**
     * @var array
     */
    protected $box;

    /**
     * Construct
     *
     * @param array $list
     */
    public function __construct(array $list = null)
    {
        $this->box = $list ?: [];
    }

    /**
     * Map a factory against a name
     *
     * @param string $name
     * @param callable $factory
     */
    public function register($name, callable $factory)
    {
        $this->box[$name] = $factory;
    }

    /**
     * Looks up and executes the factory for the specified name
     *
     * @param  string $name
     * @return mixed
     */
    public function resolve($name)
    {
        return isset($this->box[$name]) ? $this->box[$name]() : null;
    }

    /**
     * Wraps a factory so it's only invoked once, and caches its value
     *
     * @param  callable $factory
     * @return callable
     */
    public function shared(callable $factory)
    {
        $inst = null;

        return function () use (&$inst, $factory) {
            if ($inst === null) {
                $inst = $factory();
            }

            return $inst;
        };
    }

    /**
     * Creates a factory that returns the passed value in $any
     *
     * @param  mixed $any
     * @return callable
     */
    public function value($any)
    {
        return function () use ($any) { return $any; };
    }

    /**
     * Wrap a callable, providing dependencies during call-time
     *
     * @param  array $deps
     * @param  callable $func
     * @return callable
     */
    public function provide(array $deps, callable $func)
    {
        return function () use ($deps, $func) {
            $args = [];

            foreach ($deps as $dep) {
                $args[] = $this->resolve($dep);
            }

            #return $func(...$args); //PHP5.6
            return call_user_func_array($func, $args); //PHP5
        };
    }
}
