<?php
namespace Pmjones\Locator;

use StdClass;

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->locator = new Locator;
    }
    
    public function test()
    {
        $this->locator->set('mock', function () {
            return new StdClass;
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
