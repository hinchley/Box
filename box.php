<?php
/**
 * @author  Peter Hinchley
 * @license http://sam.zoy.org/wtfpl
 */

/**
 * The 'Box' class is a dependency injection service container.
 * A service container, also know as an inversion of control
 * container, is used to manage the creation and resolution of
 * objects. You register objects or object resolvers in the
 * container, and then retrieve objects from the container when
 * they are required. This practice allows you to avoid the use
 * of hardcoded class names in your code - a practice which is
 * particularly handy when unit testing.
 */
class Box {
  /**
   * The resolvers registered in the service container.
   *
   * @var array
   */
  protected static $box = array();

  /**
   * Set an object resolver in the service container.
   *
   * The resolver must be a closure that returns an object.
   *
   * <code>
   *   Box::set('foo', function() { return new Foo(); });
   * </code>
   *
   * You can manage nested object resolution by using the Box class
   * within a resolver:
   *
   * <code>
   *   Box:set('foo', function($bar) {
   *     return new Foo(Box::get('name', $bar));
   *   });
   * </code>
   *
   * @param  string  $key The unique identifier for the resolver.
   * @param  Closure $callable The closure to resolve the object.
   * @return void
   */
  public static function set($key, \Closure $callable) {
    static::$box[$key] = $callable;
  }

  /**
   * Put an existing value or object into the service container.
   *
   * Typically the entity added to the container will be a scalar
   * value, although it may be an object, or even a closure.
   *
   * <code>
   *   // Add a scalar value of 'Fred' into the container.
   *   Box::put('name', 'Fred');
   * </code>
   *
   * @param  string $key The unique identifier for the entity.
   * @param  mixed  $value The value to be returned from the
   *                container.
   * @return void
   */
  public static function put($key, $value) {
    static::$box[$key] = function() use ($value) {
      return $value;
    };
  }

  /**
   * Set a singleton object resolver in the service container.
   *
   * An object returned by this resolver will only be instantiated
   * the first time it is referenced. The same instance will be
   * returned on subsequent requests. i.e. The resolver will always
   * return a 'fixed' object.
   *
   * The resolver must be a closure that returns an object.
   *
   * <code>
   *   Box::set('singleton', function() { return new Singleton(); });
   * </code>
   *
   * @param  string  $key The unique identifier for the resolver.
   * @param  Closure $callable The closure to resolve the object.
   * @return void
   */
  public static function fix($key, \Closure $callable) {
    static::$box[$key] = function($c) use ($callable) {
      static $object;
      if (is_null($object)) $object = $callable($c);
      return $object;
    };
  }

  /**
   * Get a value or object from the service container.
   *
   * <code>
   *   // Get an instance of the 'foo' object from the container.
   *   $foo = Box::get('foo');
   *
   *   // Pass the parameter 'bar' to the 'foo' resolver.
   *   $foo = Box::get('foo', 'bar');
   * </code>
   *
   * @param  string $key The identifier of the registered resolver.
   * @param  mixed  $params The parameters passed to the resolver.
   * @return void
   * @throws InvalidArgumentException if the identifier is undefined.
   */
  public static function get($key, $params = array()) {
    if (!array_key_exists($key, static::$box)) {
      throw new \InvalidArgumentException(
        sprintf('Key "%s" is not defined.', $key)
      );
    }

    return call_user_func(static::$box[$key], $params);
  }
}