<?php namespace tests\spitfire\storage\database\drivers\mysqlpdo;

use IntegerField;
use PHPUnit\Framework\TestCase;
use Reference;
use spitfire\exceptions\PrivateException;
use spitfire\storage\database\drivers\mysqlpdo\Driver;
use spitfire\storage\database\Schema;
use spitfire\storage\database\Settings;
use spitfire\storage\database\Table;

/* 
 * The MIT License
 *
 * Copyright 2019 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class ReferenceWriteTest extends TestCase
{
	
	private $db;
	
	/**
	 * The table we're testing.
	 *
	 * @var Table
	 */
	private $table;
	private $schema;
	
	
	public function setUp() : void {
		//Just in case Mr. Bergmann decides to add code to the setUp
		parent::setUp();
		
		try {
			$this->db = new Driver(Settings::fromArray([]));
			$this->db->create();
			
			$this->schema = new Schema('test');

			$this->schema->field1 = new IntegerField(true);
			$this->schema->field2 = new Reference($this->schema);
			
			$this->db->table($this->schema);
		}
		catch (PrivateException$e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
			$this->markTestSkipped('MySQL PDO driver is not available.');
		}
	}
	
	public function tearDown() : void {
		$this->db->destroy();
	}
	
	public function testWrite() {
		
		$r1 = $this->db->table($this->schema)->newRecord();
		$r1->field2 = $r1;
		
		$this->expectException(PrivateException::class);
		$r1->store();
	}
	
	public function testWrite2() {
		
		$r1 = $this->db->table('test')->newRecord();
		
		$r1->store();
		$r1->field2 = $r1;
		
		$r1->store();
		
		$this->assertEquals($r1->field2->_id, $r1->_id);
	}
}