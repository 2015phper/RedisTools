<?php
namespace RedisTools\Db\Field;

/**
 * Test class for SimpleValue.
 * Generated by PHPUnit on 2011-06-27 at 22:15:25.
 */
class SimpleValueTest extends TestBase
{
	/**
	 * @var SimpleValue
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new SimpleValue($this->getValueObject());
		parent::setUp();
	}

	public function testHasObjectValue()
	{
		$this->assertTrue($this->object->hasObjectValue());
	}

	public function testOnSave()
	{
		$this->assertTrue($this->object->onSave());
	}

	public function testOnDelete()
	{
		$this->assertTrue($this->object->onDelete());
	}
}
?>
