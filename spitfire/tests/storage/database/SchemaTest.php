<?php namespace tests\storage\database;

use IntegerField;
use PHPUnit\Framework\TestCase;
use spitfire\exceptions\PrivateException;
use spitfire\model\Field;
use spitfire\model\Index;
use spitfire\storage\database\Schema;
use function db;

class SchemaTest extends TestCase
{
	
	/**
	 * Ensures that a schema, when created, has the default _id field.
	 */
	public function testCreate() {
		$schema = new Schema('test');
		
		#Test if ID exists and is a Integer
		$this->assertInstanceOf(Field::class, $schema->_id);
		$this->assertInstanceOf(IntegerField::class, $schema->_id);
		
		#Test if the name is actually test
		$this->assertEquals('test', $schema->getName());
	}
	
	public function testReadingAnUnexistingField() {
		$schema = new Schema('test');
		
		$this->expectException(PrivateException::class);
		$schema->test;
	}
	
	public function testPrimary() {
		$schema = new Schema('test');
		$this->assertContainsOnlyInstancesOf(Field::class, $schema->getPrimary()->getFields()->toArray());
	}
	
	/**
	 * This test assumes that the table will be located inside a namespace. In 
	 * this case the schema should return a table name that contains hyphens instead
	 * of backslashes since tables do accept hyphens and don't accept backslashes.
	 * 
	 * @covers \spitfire\storage\database\Schema::getName
	 * @covers \spitfire\storage\database\Schema::getTableName
	 */
	public function testComplexTableName() {
		$schema = new Schema('test\test');
		$this->assertEquals('test\test', $schema->getName(), 'The schema name should be the class name without Model suffix.');
		$this->assertEquals('test-test', $schema->getTableName(), 'The table name should have replaced hyphens.');
	}
	
	/**
	 * This test ensures that the model acquires fields properly in the event of 
	 * copying them from one Schema to another.
	 */
	public function testSetFields() {
		$a = new Schema('test');
		$b = new Schema('test');
		
		$b->a = new IntegerField();
		$a->setFields($b->getFields());
		
		$this->assertInstanceOf(IntegerField::class, $a->a);
		$this->assertEquals($a, $a->a->getModel());
	}
	
	/**
	 * Tests whether the Schema allows removal of a field that is no longer needed
	 * for the usage of the database.
	 * 
	 * Unsetting a field is usually only needed when working with very special case
	 * schemas or when using different PKs than the default _id
	 * 
	 */
	public function testUnsettingField() {
		$a = new Schema('test');
		unset($a->_id);
		
		$this->assertEquals(null, isset($a->_id));
		$this->assertEquals(0, $a->getIndexes()->count());
	}
	
	public function testUnsettingFieldTwice() {
		$a = new Schema('test');
		
		#The first time it should run perfectly fine
		unset($a->_id);
		
		#The second time unsetting should fail.
		$this->expectException(PrivateException::class);
		unset($a->_id);
	}
	
	/**
	 * Tests whether the Schema allows removal of a field that is no longer needed
	 * for the usage of the database.
	 * 
	 * Unsetting a field is usually only needed when working with very special case
	 * schemas or when using different PKs than the default _id
	 */
	public function testGettingField() {
		$a = new Schema('test');
		
		$this->assertEquals(null, $a->getField('b'), 'There is no field "B" it should fail');
		$this->assertInstanceOf(Field::class, $a->getField('_id'), 'The id field should be present');
	}
	
	public function testMakePhysicalFields() {
		$schema = new Schema('test');
		$table  = db()->table($schema);
		
		$this->assertEquals(1, count($table->getLayout()->getFields()));
	}
	
	/*
	 * Ensures that the schema creates a bunch of indexes that the system can read
	 * appropriately and therefore process as expected.
	 */
	public function testMakeIndex() {
		/*
		 * Prepare a test schema with a bunch of fields.
		 */
		$schema = new Schema('test');
		$schema->a = new \IntegerField();
		$schema->b = new \IntegerField();
		
		/*
		 * Index the two columns we just created.
		 */
		$index = $schema->index($schema->a, $schema->b)->unique();
		
		/*
		 * Run assertions to ensure that the index was properly created and 
		 * every component works as expected.
		 */
		$this->assertInstanceOf(Index::class, $index);
		$this->assertEquals(2, $schema->getIndexes()->count());
		$this->assertEquals(true, $index->isUnique());
		
		/*
		 * Since the schema only contains the primary and a unique key, every key
		 * in the bunch should be unique.
		 */
		$schema->getIndexes()->each(function ($e) { $this->assertEquals(true, $e->isUnique()); });
	}
}