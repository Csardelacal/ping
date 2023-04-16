<?php namespace spitfire\model\adapters;

use spitfire\exceptions\PrivateException;
use spitfire\Model;
use spitfire\storage\database\Field;

/**
 * The reference adapter allows models to provide models for objects that are
 * just referenced. This prevents the user from having to resolve IDs manually.
 *
 * This adapter will return the appropriate model for the given table.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class ReferenceAdapter extends BaseAdapter
{
	
	/**
	 * Represents the data that the DBMS holds. This may be Model, Query or null.
	 *
	 * @var Model|mixed[]
	 */
	private $remote;
	
	/**
	 *
	 * @var Model|mixed[]
	 */
	private $local;
	
	private function resolve() {
		/*
		 * If we already have a model, we do no longer need to fetch it.
		 */
		if ($this->remote instanceof Model || $this->remote === null) {
			return;
		}
		
		/*
		 * Fetch the parent record from the DBMS. For this, the physical fields of
		 * the "model field" are extracted and used to query the parent.
		 *
		 * This also works for fields that already have remote primary keys.
		 */
		$table = $this->getField()->getTarget()->getTable();
		$query = $table->getAll();
		$physical = $this->getField()->getPhysical();
		
		foreach ($physical as $p) {
			/* @var $p Field */
			$query->addRestriction($p->getReferencedField()->getName(), $this->remote[$p->getName()]);
		}
		
		$this->remote = $this->local = $query->first();
	}
	
	/**
	 * Receives data from the DBMS. This endpoint allows the DBMS to set data to
	 * the adapter. This means that the data will be an array containing the data
	 * pertaining to the logical field (this may be more than one column)
	 *
	 * @param mixed[] $data
	 * @return void
	 */
	public function dbSetData($data) {
		$this->remote = $this->local = $data;
	}
	
	/**
	 * This method allows the DBMS to fetch data to be written to the database.
	 *
	 * @return type
	 * @throws PrivateException
	 */
	public function dbGetData() {
		$field = $this->getField();
		$physical = $field->getPhysical();
		$_return = array();
		
		/*
		 * If the data has not yet been committed to the database, the system
		 * should prevent the user from writing the data to the DBMS
		 */
		if ($this->local instanceof Model && $this->local->isNew()) {
			throw new PrivateException('Dependencies need to be stored first.', 1906110859);
		}
		elseif ($this->local instanceof Model) {
			#Get the raw data from the donor model
			$modeldata = $this->local->getPrimaryData();
			foreach ($physical as $p) {
				$_return[$p->getName()] = $modeldata[$p->getReferencedField()->getName()];
			}
		}
		/*
		 * This is a special case. When this section becomes active, it usually means
		 * that the data is either being written or used in an update clause.
		 *
		 * We cannot just resolve the data, because in special scenarios, a circular
		 * reference will crash Spitfire.
		 */
		elseif (is_array($this->local)) {
			return $this->local;
		}
		elseif ($this->local === null) {
			foreach ($physical as $p) {
				$_return[$p->getName()] = null;
			}
		}
		else {
			throw new PrivateException('Adapter holds invalid data');
		}
		
		return $_return;
	}
	
	public function usrGetData() {
		$this->resolve();
		return $this->local;
	}
	
	public function usrSetData($data) {
		//Check if the incoming data is an int
		if (!$data instanceof Model && !is_null($data)) {
			throw new PrivateException('This adapter only accepts models');
		}
		//Make sure the finally stored data is an integer.
		$this->local = $data;
	}
	
	public function isSynced() {
		/*
		 * If the data is still an array, it's impossible that the user has edited
		 * it.
		 */
		if (is_array($this->local)) {
			return true;
		}
		
		$pka = $this->remote instanceof Model? $this->remote->getPrimaryData() : null;
		$pkb = $this->local instanceof Model? $this->local->getPrimaryData() : null;
		
		return $pka == $pkb;
	}
	
	
	/**
	 * Sets the data as stored to the database and therefore as synced. After
	 * committing, rolling back will return the current value.
	 */
	public function commit() {
		#Now we can safely say that the data stored on the remote and local sets
		#is equal. Therefore we can replace the old remote value.
		$this->remote = $this->local;
	}
	
	/**
	 * Resets the data to the status the database holds. This is especially
	 * interesting if you want to undo certain changes.
	 */
	public function rollback() {
		$this->local = $this->remote;
	}
}
