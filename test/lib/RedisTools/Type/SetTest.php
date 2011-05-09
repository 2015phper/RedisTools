<?php
namespace RedisTools\Type;
/**
 * Test class for Set.
 * Generated by PHPUnit on 2011-04-06 at 22:26:21.
 */
class SetTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var Set
	 */
	protected $object;

	protected $testKey = 'key';

	protected $testSets = array();
	
	protected function setUp()
	{
		$redis = new \Redis();
		$redis->pconnect('127.0.0.1');
		
		$this->object = new Set($this->testKey, $redis);
	}

	protected function tearDown()
	{
		$this->object->delete();
		foreach($this->testSets as $set)
		{
			$set->delete();
		}
	}
	
	public function testAddValue()
	{
		$this->assertTrue(
			$this->object->addValue( 'asdf' )
		);
		
		$this->assertFalse(
			$this->object->addValue( 'asdf' )
		);
		
		$this->assertTrue(
			$this->object->addValue( 'qwer' )
		);
	}
	
	public function testDeleteValueOnEmptyKey()
	{
		$this->assertFalse(
			$this->object->deleteValue( 'asdf' )
		);
	}
	
	public function testDeleteValue()
	{
		$this->object->addValue('asdf');
		
		$this->assertFalse(
			$this->object->deleteValue( 'qwer' )
		);
		
		$this->assertTrue(
			$this->object->deleteValue( 'asdf' )
		);
		
		$this->assertFalse(
			$this->object->deleteValue( 'asdf' )
		);
		
	}
	
	public function testContainsOnEmptyKey()
	{
		$this->assertFalse(
			$this->object->contains('asdf')
		);
	}
	
	public function testContains()
	{
		$this->assertFalse(
			$this->object->contains('asdf')
		);
		
		$this->object->addValue('asdf');
		
		$this->assertTrue(
			$this->object->contains('asdf')
		);
		
		$this->assertFalse(
			$this->object->contains('qwer')
		);
		
	}
	
	public function testMoveValueToSet()
	{
		$this->object->addValue('v1');
		$this->object->addValue('v2');
		$this->object->addValue('v3');
		
		$this->assertTrue(
			$this->object->contains('v1')
		);
		
		$redis = new \Redis();
		$redis->pconnect('127.0.0.1');
		
		$redis->delete('testkey');
		$set = new Set('testkey', $redis);
		
		$this->assertTrue(
			$this->object->moveValueToSet('v1', $set )
		);
		
		$this->assertTrue(
			$set->contains('v1')
		);
		
		$this->assertFalse(
			$this->object->contains('v1')
		);
		
		$this->assertFalse(
			$this->object->moveValueToSet('v5', $set )
		);
		
		$this->object->addValue('v1');
		
		$this->assertTrue(
			$this->object->moveValueToSet('v1', $set )
		);
		
		$this->assertFalse(
			$this->object->contains('v1')
		);
		
		$set->delete();
	}
	
	public function testCountOnEmptyKey()
	{
		$this->assertEquals(0, 
			$this->object->count()
		);
		
	}
	
	public function testCount()
	{
		$this->object->addValue('asdf');
		
		$this->assertEquals(1, 
			$this->object->count()
		);
		
		$this->object->addValue('v1');
		$this->object->addValue('v2');
		$this->object->addValue('v3');
		
		$this->assertEquals(4, 
			count($this->object)
		);
	}


	public function testPopOnEmptyKey()
	{
		$this->assertFalse(
			$this->object->pop()
		);
	}
	
	public function testPop()
	{
		$values = array(
			'v1', 'v2', 'v3', 'v4'
		);
		
		foreach($values as $value)
		{
			$this->object->addValue($value);
		}
		
		$this->assertTrue(
			in_array( $this->object->pop(), $values )
		);
		
		$this->assertEquals(3, count($this->object));
		
		$this->assertTrue(
			in_array( $this->object->pop(), $values )
		);
		
		$this->assertEquals(2, count($this->object));

		$this->assertTrue(
			in_array( $this->object->pop(), $values )
		);
		
		$this->assertEquals(1, count($this->object));

	}
	
	public function testGetValuesOnEmptyKey()
	{
		$result = $this->object->getValues();
		$this->assertInternalType('array', $result);
		
		$this->assertEquals(0, count($result));
	}
	
	public function testGetValues()
	{
		$values = array('v1', 'v2', 'v3');
		foreach($values as $value)
		{
			$this->object->addValue($value);
		}
		
		$result = $this->object->getValues();
		$this->assertInternalType('array', $result);
		
		foreach ($result as $value)
		{
			$this->assertTrue(
				in_array( $value, $values)
			);
		}
	}
	
	public function testGetDiffOnEmptyKeyWithNoSetsToCompare()
	{
		$result = $this->object->getDiff();
		$this->assertInternalType('array', $result);
		$this->assertEquals(0, count($result));
	}
	
	public function testGetDiffOnEmptyKeyAndStoreResultNoSetsToCompare()
	{
		$result = $this->object->getDiff(array(), true, 'testkey');
		$this->assertType('\RedisTools\Type\Set', $result);
		$this->assertEquals(0, count($result));
		$result->delete();
	}
	
	public function testGetDiffNoSetsToCompare()
	{
		$values = array('v1', 'v2');
		$this->fillSetWithTestValues($this->object, $values);
		
		$result = $this->object->getDiff();
		$this->assertInternalType('array', $result);
		$this->assertEquals(count($values), count($result));
		
		foreach($result as $value)
		{
			$this->assertTrue(in_array($value, $values));
		}
	}
	
	public function testGetDiffAndStoreResultNoSetsToCompare()
	{
		$values = array('v1', 'v2');
		$this->fillSetWithTestValues($this->object, $values);
		
		$result = $this->object->getDiff(array(), true, 'testkey');
		$this->assertType('\RedisTools\Type\Set', $result);
		$this->assertEquals(count($values), count($result));
		$result->delete();
	}
	
	public function testGetDiffWithOneSetToCompare()
	{
		$values1 = array('v1', 'v2', 'v3', 'v5');
		$values2 = array('v4', 'v2', 'v3');
		
		$this->fillSetWithTestValues($this->object, $values1);
		$setCompare = $this->getTestSet('testkey1', $values2);
		
		$result = $this->object->getDiff( $setCompare );
		$this->assertInternalType('array', $result);
		$this->assertContains('v1', $result);
		$this->assertContains('v5', $result);
		$this->assertNotContains('v2', $result);
		$this->assertNotContains('v3', $result);
		$this->assertNotContains('v4', $result);
	}
	
	public function testGetDiffWithOneSetToCompareAndStoreResult()
	{
		$values1 = array('v1', 'v2', 'v3', 'v5');
		$values2 = array('v4', 'v2', 'v3');
		
		$this->fillSetWithTestValues($this->object, $values1);
		$setCompare = $this->getTestSet('testkey1', $values2);
		
		$result = $this->object->getDiff( array($setCompare), true, 'resultkey' );
		$this->assertType('\RedisTools\Type\Set', $result);
		$this->assertTrue($result->contains('v1'));
		$this->assertTrue($result->contains('v5'));
		
		$this->assertFalse($result->contains('v2'));
		$this->assertFalse($result->contains('v3'));
		$this->assertFalse($result->contains('v4'));
		
		$result->delete();
	}
	
	public function testGetDiffWithMultipleSetsToCompareAndStoreResult()
	{
		$values1 = array('v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8');
		$values2 = array('v1', 'v2', 'v3');
		$values3 = array('v5', 'v6', 'v7');
		
		$this->fillSetWithTestValues($this->object, $values1);
		$setCompare = $this->getTestSet('testkey1', $values2);
		$setCompare1 = $this->getTestSet('testkey2', $values3);
		
		$result = $this->object->getDiff( array($setCompare, $setCompare1), true, 'resultkey' );
		$this->assertType('\RedisTools\Type\Set', $result);
		$this->assertTrue($result->contains('v4'));
		$this->assertTrue($result->contains('v8'));
		
		$this->assertFalse($result->contains('v2'));
		$this->assertFalse($result->contains('v7'));
		
		$result->delete();
	}

	protected function fillSetWithTestValues( Set $set, $values = array() )
	{
		foreach($values as $value)
		{
			$set->addValue($value);
		}
	}
	
	protected function getTestSet( $key, $testValues = array() )
	{
		$set = new Set($key, $this->getRedis());
		$this->fillSetWithTestValues($set, $testValues);
		$this->testSets[]= $set;
		return $set;
	}
	
	protected function getRedis()
	{
		return $this->object->getRedis();
	}
	

}

/*
--------------------------------------------------------
sInter
--------------------------------------------------------
*Description*
Returns the members of a set resulting from the intersection of all the sets held at the specified keys. If just a single key is specified, then this command produces the members of this set. If one of the keys is missing, FALSE is returned.

*Parameters*
key1, key2, keyN: keys identifying the different sets on which we will apply the intersection.

*Return value*
Array, contain the result of the intersection between those keys. If the intersection beteen the different sets is empty, the return value will be empty array.

*Examples*
$redis->sAdd('key1', 'val1');
$redis->sAdd('key1', 'val2');
$redis->sAdd('key1', 'val3');
$redis->sAdd('key1', 'val4');

$redis->sAdd('key2', 'val3');
$redis->sAdd('key2', 'val4');

$redis->sAdd('key3', 'val3');
$redis->sAdd('key3', 'val4');

var_dump($redis->sInter('key1', 'key2', 'key3'));
Output:

array(2) {
  [0]=>
  string(4) "val4"
  [1]=>
  string(4) "val3"
}
*/

/*
--------------------------------------------------------
sInterStore
--------------------------------------------------------
*Description*
Performs a sInter command and stores the result in a new set.

*Parameters*
Key: dstkey, the key to store the diff into.

Keys: key1, key2... keyN. key1..keyN are intersected as in sInter.

*Return value*
INTEGER: The cardinality of the resulting set, or FALSE in case of a missing key.

*Example*
$redis->sAdd('key1', 'val1');
$redis->sAdd('key1', 'val2');
$redis->sAdd('key1', 'val3');
$redis->sAdd('key1', 'val4');

$redis->sAdd('key2', 'val3');
$redis->sAdd('key2', 'val4');

$redis->sAdd('key3', 'val3');
$redis->sAdd('key3', 'val4');

var_dump($redis->sInterStore('output', 'key1', 'key2', 'key3'));
var_dump($redis->sMembers('output'));
Output:

int(2)

array(2) {
  [0]=>
  string(4) "val4"
  [1]=>
  string(4) "val3"
}
*/

/*
--------------------------------------------------------
sUnion
--------------------------------------------------------
*Description*
Performs the union between N sets and returns it.

*Parameters*
Keys: key1, key2, ... , keyN: Any number of keys corresponding to sets in redis.

*Return value*
Array of strings: The union of all these sets.

*Example*
$redis->delete('s0', 's1', 's2');

$redis->sAdd('s0', '1');
$redis->sAdd('s0', '2');
$redis->sAdd('s1', '3');
$redis->sAdd('s1', '1');
$redis->sAdd('s2', '3');
$redis->sAdd('s2', '4');

var_dump($redis->sUnion('s0', 's1', 's2'));
Return value: all elements that are either in s0 or in s1 or in s2.
array(4) {
  [0]=>
  string(1) "3"
  [1]=>
  string(1) "4"
  [2]=>
  string(1) "1"
  [3]=>
  string(1) "2"
}
*/

/*
-------------------------------------------------------- 
sUnionStore
--------------------------------------------------------
*Description*
Performs the same action as sUnion, but stores the result in the first key

*Parameters*
Key: dstkey, the key to store the diff into.

Keys: key1, key2, ... , keyN: Any number of keys corresponding to sets in redis.

*Return value*
INTEGER: The cardinality of the resulting set, or FALSE in case of a missing key.

*Example*
$redis->delete('s0', 's1', 's2');

$redis->sAdd('s0', '1');
$redis->sAdd('s0', '2');
$redis->sAdd('s1', '3');
$redis->sAdd('s1', '1');
$redis->sAdd('s2', '3');
$redis->sAdd('s2', '4');

var_dump($redis->sUnionStore('dst', 's0', 's1', 's2'));
var_dump($redis->sMembers('dst'));
Return value: the number of elements that are either in s0 or in s1 or in s2.
int(4)
array(4) {
  [0]=>
  string(1) "3"
  [1]=>
  string(1) "4"
  [2]=>
  string(1) "1"
  [3]=>
  string(1) "2"
}
*/

/*
--------------------------------------------------------
sDiff
--------------------------------------------------------
*Description*
Performs the difference between N sets and returns it.

*Parameters*
Keys: key1, key2, ... , keyN: Any number of keys corresponding to sets in redis.

*Return value*
Array of strings: The difference of the first set will all the others.

*Example*
$redis->delete('s0', 's1', 's2');

$redis->sAdd('s0', '1');
$redis->sAdd('s0', '2');
$redis->sAdd('s0', '3');
$redis->sAdd('s0', '4');

$redis->sAdd('s1', '1');
$redis->sAdd('s2', '3');

var_dump($redis->sDiff('s0', 's1', 's2'));
Return value: all elements of s0 that are neither in s1 nor in s2.
array(2) {
  [0]=>
  string(1) "4"
  [1]=>
  string(1) "2"
}
*/

/*
--------------------------------------------------------
sDiffStore
--------------------------------------------------------
*Description*
Performs the same action as sDiff, but stores the result in the first key

*Parameters*
Key: dstkey, the key to store the diff into.

Keys: key1, key2, ... , keyN: Any number of keys corresponding to sets in redis

*Return value*
INTEGER: The cardinality of the resulting set, or FALSE in case of a missing key.

*Example*
$redis->delete('s0', 's1', 's2');

$redis->sAdd('s0', '1');
$redis->sAdd('s0', '2');
$redis->sAdd('s0', '3');
$redis->sAdd('s0', '4');

$redis->sAdd('s1', '1');
$redis->sAdd('s2', '3');

var_dump($redis->sDiffStore('dst', 's0', 's1', 's2'));
var_dump($redis->sMembers('dst'));
Return value: the number of elements of s0 that are neither in s1 nor in s2.
int(2)
array(2) {
  [0]=>
  string(1) "4"
  [1]=>
  string(1) "2"
}

*/
