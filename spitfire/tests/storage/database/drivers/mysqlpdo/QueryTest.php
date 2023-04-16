<?php namespace tests\spitfire\storage\database\drivers\mysqlpdo;

use IntegerField;
use PHPUnit\Framework\TestCase;
use spitfire\exceptions\PrivateException;
use spitfire\storage\database\AggregateFunction;
use spitfire\storage\database\drivers\mysqlpdo\Driver;
use spitfire\storage\database\Schema;
use spitfire\storage\database\Settings;
use spitfire\storage\database\Table;
use StringField;

class QueryTest extends TestCase
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
			$this->schema->field2 = new StringField(255);

			$this->table = new Table($this->db, $this->schema);
			$this->table->getLayout()->create();
		}
		catch (PrivateException$e) {
			$this->markTestSkipped('MySQL PDO driver is not available.');
		}
	}
	
	public function tearDown() : void {
		$this->db->destroy();
	}
	
	public function testQuery() {
		$record = $this->table->newRecord();
		$record->field1 = 1;
		$record->field2 = 'Test';
		$record->store();
		
		$result = $this->table->get('field1', 1)->fetch();
		$this->assertNotEquals(null, $result);
	}
	
	/*
	 * To test that aggregation works properly, we run a script that fills our demo
	 * table with values between 0 and 4 multiple times but fills in more 0s than
	 * 4s, allowing us to test that the aggregation works properly.
	 */
	public function testQuery2() {
		
		/*
		 * Fill the table with sample data. Just to reiterate, the data is filled
		 * with field1 values between 0 and 4.
		 * 
		 * How they're exactly distributed is irrelevant, the important fact is that
		 * the 4 is the least common, and the 0 is the most common.
		 */
		foreach ([5, 3, 1, 1, 2] as $j) {
			for ($i = 0; $i < $j; $i++) {
				$record = $this->table->newRecord();
				$record->field1 = $i;
				$record->field2 = 'Test';
				$record->store();
			}
		}
		
		/*
		 * Prepare a query and the aggregate function for counting the data.
		 */
		$query = $this->table->get('field2', 'Test');
		$fn    = new AggregateFunction($query->getQueryTable()->getField('_id'), AggregateFunction::AGGREGATE_COUNT);
		
		/*
		 * Tell the query to aggregate the data by field1, which we're using to 
		 * associate records that are repeating.
		 */
		$query->aggregateBy([$query->getQueryTable()->getField('field1')]);
		$query->setOrder($fn, 'DESC');
		
		/*
		 * Execute the query, for this we pass the field we're aggregating by (so
		 * we get data out of it) and the aggregating function.
		 */
		$result = $query->execute([$query->getQueryTable()->getField('field1'), $fn]);
		$rows   = collect();
		
		while($record = $result->fetchArray()) { $rows->push($record); }
		
		/*
		 * Check the values we received do actually make sense.
		 */
		$this->assertEquals(5, $rows->count());
		$this->assertEquals(0, $rows[0]['field1']);
		$this->assertEquals(4, $rows->last()['field1']);
		$this->assertEquals(5, $rows[0][$fn->getAlias()]);
	}
	
}
