<?php namespace spitfire\storage\database\drivers\mysqlpdo;

use spitfire\exceptions\PrivateException;
use spitfire\model\Field;
use spitfire\storage\database\drivers\mysqlpdo\CompositeRestriction;
use spitfire\storage\database\drivers\sql\SQLQuery;
use spitfire\storage\database\QueryField;
use spitfire\storage\database\QueryTable;
use spitfire\storage\database\Relation;
use spitfire\storage\database\Table;

class Query extends SQLQuery
{
	public function execute($fields = null, $offset = null, $max = null) {
		
		$this->setAliased(false);
		
		
		#Import tables for restrictions from remote queries
		$plan       = $this->makeExecutionPlan();
		$last       = array_shift($plan);
		$joins      = Array();
		$retModel   = empty($fields);
		
		foreach ($plan as $q) {
			$joins[] = sprintf('LEFT JOIN %s ON (%s)', $q->getQueryTable()->definition(), implode(' AND ', $q->getRestrictions()));
		}
		
		$selectstt    = 'SELECT';
		$fromstt      = 'FROM';
		$tablename    = $last->getQueryTable()->definition();
		$wherestt     = 'WHERE';
		$restrictions = $last->getRestrictions();
		$orderstt     = 'ORDER BY';
		$order        = $this->getOrder();
		$groupbystt   = 'GROUP BY';
		$groupby      = $this->aggregate;
		$limitstt     = 'LIMIT';
		$limit        = $offset . ', ' . $max;
		
		if ($fields === null) {
			$fields = $last->getQueryTable()->getFields();
		}
		else {
			$fields = collect($fields)->each(function ($e) { return $e instanceof \spitfire\storage\database\AggregateFunction? sprintf('%s(%s) AS %s', $e->getOperation(), $e->getField(), $e->getAlias()) : $e; })->toArray();
		}
		
		if (!empty($this->calculated)) {
			foreach ($this->calculated as $calculated) {
				$fields[] = sprintf('%s(%s) AS %s', $calculated->getOperation(), $calculated->getField(), $calculated->getAlias());
			}
		}
		
		$join = implode(' ', $joins);
		
		#Restrictions
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		else {
			$restrictions = implode(' AND ', $restrictions);
		}
		
		if ($max === null) {
			$limitstt = '';
			$limit    = '';
		}
		
		if (empty($order)) {
			$orderstt = '';
			$order    = '';
		}
		else {
			$field = $order['field'] instanceof \spitfire\storage\database\AggregateFunction? $order['field']->getAlias() : $order['field'];
			$order = "{$field} {$order['mode']}";
		}
		
		if (empty($groupby)) {
			$groupbystt = '';
			$groupby    = '';
		}
		else {
			$groupby = implode(', ', $groupby);
		}
		
		$stt = array_filter(Array( $selectstt, implode(', ', $fields), $fromstt, $tablename, $join, 
		    $wherestt, $restrictions, $groupbystt, $groupby, $orderstt, $order, $limitstt, $limit));
		
		return new ResultSet($this->getTable(), $this->getTable()->getDb()->execute(implode(' ', $stt)));
		
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20171110
	 * @param QueryField $field
	 * @param type $value
	 * @param type $operator
	 * @return MysqlPDORestriction
	 */
	public function restrictionInstance(QueryField$field, $value, $operator) {
		return new MysqlPDORestriction($this, $field, $value, $operator);
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20171110
	 * @param QueryField $field
	 * @return \spitfire\storage\database\drivers\MysqlPDOQueryField|QueryField
	 */
	public function queryFieldInstance($field) {
		trigger_error('Deprecated: mysqlPDOQuery::queryFieldInstance is deprecated', E_USER_DEPRECATED);
		
		if ($field instanceof QueryField) {return $field; }
		return new MysqlPDOQueryField($this, $field);
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20171110
	 * @param type $table
	 * @return \spitfire\storage\database\drivers\MysqlPDOQueryTable
	 * @throws PrivateException
	 */
	public function queryTableInstance($table) {
		if ($table instanceof Relation) { $table = $table->getTable(); }
		if ($table instanceof QueryTable) { $table = $table->getTable(); }
		
		
		if (!$table instanceof Table) { throw new PrivateException('Did not receive a table as parameter'); }
		
		return new MysqlPDOQueryTable($this, $table);
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20171110
	 */
	public function compositeRestrictionInstance(Field $field = null, $value, $operator) {
		return new CompositeRestriction($this, $field, $value, $operator);
	}
	
	/**
	 * 
	 * @fixme
	 */
	public function delete() {
		
		
		$this->setAliased(false);
		
		#Declare vars
		$selectstt    = 'DELETE';
		$fromstt      = 'FROM';
		$tablename    = $this->getTable();
		$wherestt     = 'WHERE';
		/** @link http://www.spitfirephp.com/wiki/index.php/Database/subqueries Information about the filter*/
		$restrictions = array_filter($this->getRestrictions(), Array('spitfire\storage\database\Query', 'restrictionFilter'));
		
		
		#Import tables for restrictions from remote queries
		$subqueries = $this->getPhysicalSubqueries();
		$joins      = Array();
		
		foreach ($subqueries as $q) {
			$joins[] = sprintf('LEFT JOIN %s ON (%s)', $q->getQueryTable()->definition(), implode(' AND ', $q->getRestrictions()));
		}
		
		$join = implode(' ', $joins);
		
		#Restrictions
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		else {
			$restrictions = implode(' AND ', $restrictions);
		}
		
		$stt = array_filter(Array( $selectstt, $fromstt, $tablename, $join, 
		    $wherestt, $restrictions));
		
		$this->getTable()->getDb()->execute(implode(' ', $stt));
	}
}