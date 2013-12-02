<?php
/**
 *
 * A ServiceLocator implementation and PHPUnit test.
 *
 * @package Pmjones
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Pmjones;

use UnexpectedValueException;

/**
 *
 * A ServiceLocator implementation for creating and retaining object instances
 * by name; the object instances are created using callable factories.
 *
 * @package Pmjones
 *
 */
class ServiceLocator
{
    /**
     *
     * A registry of callable factories to create object instances.
     *
     * @var array
     *
     */
    protected $factories = array();

    /**
     *
     * A registry of object instances created by the factories.
     *
     * @var array
     *
     */
    protected $instances = array();
    
    /**
     *
     * Constructor.
     *
     * @param array $factories An array of key-value pairs where the key is a
     * name and the value is a callable that returns an object instance.
     *
     */
    public function __construct(array $factories = array())
    {
        $this->factories = $factories;
    }

    /**
     *
     * Sets a callable factory to create an object instance by name; removes
     * any existing object instance under that name.
     *
     * @param string $name The object name.
     *
     * @param callable $callable A callable that returns an object. We avoid
     * typehinting so we can register factories that might not yet be
     * available.
     *
     * @return null
     *
     */
    public function set($name, $callable)
    {
        $this->factories[$name] = $callable;
        unset($this->instances[$name]);
    }

    /**
     *
     * Gets an object instance by name; if it has not been created yet, its
     * callable factory will be invoked and the instance will be retained.
     *
     * @param string $name The name of the object instance to retrieve.
     *
     * @return object An object instance.
     *
     * @throws UnexpectedValueException when an unrecognized object name is
     * given.
     *
     */
    public function get($name)
    {
        if (! isset($this->factories[$name])) {
            throw new UnexpectedValueException($name);
        }

        if (! isset($this->instances[$name])) {
            $callable = $this->factories[$name];
            $this->instances[$name] = $callable();
        }

        return $this->instances[$name];
    }
}

/**
 *
 * A PHPUnit test for the above ServiceLocator implementation.
 *
 * @package Pmjones
 *
 */
class ServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->locator = new ServiceLocator;
    }
    
    public function testSetAndGet()
    {
        $this->locator->set('mock', function () {
            return new \StdClass;
        });
        
        $instance1 = $this->locator->get('mock');
        $this->assertInstanceOf('StdClass', $instance1);
        
        $instance2 = $this->locator->get('mock');
        $this->assertSame($instance1, $instance2);
    }
    
    public function testGetNoSuchInstance()
    {
        $this->setExpectedException('UnexpectedValueException');
        $this->locator->get('NoSuchInstance');
    }
}

