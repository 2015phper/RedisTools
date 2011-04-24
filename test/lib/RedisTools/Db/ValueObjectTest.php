<?php
namespace RedisTools\Db;
/**
 * Test class for ValueObject.
 * Generated by PHPUnit on 2011-04-21 at 23:12:55.
 */
class ValueObjectTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var ValueObjectDummy
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new ValueObjectDummy();
		
	}

	public function testGetReflector()
	{
		$this->assertType('\RedisTools\Utils\Reflection', $this->object->getReflector());
	}

	public function testSetReflector()
	{
		$object = new \stdClass();
		$this->object->setReflector($object);
		$this->assertEquals($object, $this->object->getReflector());
	}
	
	public function testHasSimpleValueProperty()
	{
		$this->assertType('\RedisTools\Db\Field\SimpleValue',
			$this->object->simpleValue
		);
	}
	
	public function testHasUniqueIdentifierProperty()
	{
		$this->assertType('\RedisTools\Db\Field\UniqueIdentifier',
			$this->object->uniqueIdentifier
		);
	}
	
	public function testSetGetRedisPropertySimpleValue()
	{
		$value = 'asdf';
		$this->object->set('simpleValue', $value);
		
		$this->assertEquals($value, $this->object->get('simpleValue'));
	}
	
	/**
	 * @expectedException \RedisTools\Exception
	 */
	public function testSetNonexistingRedisProperty()
	{
		$this->object->set('nonExisting', 'asdf');
	}
	
	/**
	 * @expectedException \RedisTools\Exception
	 */
	public function testGetNonexistingRedisProperty()
	{
		$this->object->get('nonExisting', 'asdf');
	}

}

?>
