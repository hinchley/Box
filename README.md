Box
===
Box is a dependency injection service container.

A service container, also known as an inversion of control container, is used to manage the creation and resolution of objects. You register objects or object resolvers in the container, and then retrieve objects from the container when they are required. This practice allows you to avoid the use of hardcoded class names in your code - a practice which is particularly handy when unit testing.

Usage
-----
### Set
Use the ``set`` method to register an object resolver in the service container.

The resolver must be a closure that returns an object.

    Box::set('foo', function() { return new Foo(); });

You can manage nested object resolution by using the ``Box`` class within a resolver:

    Box:set('foo', function($bar) {
	    return new Foo(Box::get('name', $bar));
    });

### Put
Use the ``put`` method to add an existing value or object into the service container.

Typically the entity added to the container will be a scalar value, although it may be an object, or even a closure.

    // Add a scalar value of 'Fred' into the container.
    Box::put('name', 'Fred');

### Fix
Use the ``fix`` method to set a singleton object resolver in the service container.

An object returned by this resolver will only be instantiated the first time it is referenced. The same instance will be returned on subsequent requests. i.e. The resolver will always return a 'fixed' object.

The resolver must be a closure that returns an object.

    Box::set('singleton', function() { return new Singleton(); });

### Get
Use the ``get`` method to retrieve a value or object from the service container.

    // Get an instance of the 'foo' object from the container.
    $foo = Box::get('foo');

    // Pass the parameter 'bar' to the 'foo' resolver.
	$foo = Box::get('foo', 'bar');