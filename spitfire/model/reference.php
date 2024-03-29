<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\ReferenceAdapter;
use spitfire\storage\database\Schema;

/**
 * Elements of this class indicate a connection between two Models, this allows
 * spitfire to let you use relational databases just like they were objects
 * (connected to each other). While real elements are scattered around tables
 * in no apparent manner.
 * 
 * This class doesn't indicate how the fields inside objects relate as their
 * names are automatically inherited by the source model when referencing. Names
 * for the fields are always given in a <i>alias</i>_<i>srcfield</i> fashion to 
 * improve understanding.
 * 
 * [NOTICE] The model doesn't check for the availability of field names, in case
 * you have duplicate field names due to references the system may behave in an 
 * unexpected way.
 * 
 * @author César de la cal <cesar@magic3w.com>
 * @last-revision 2013-05-16
 */
class Reference extends Field
{
	/**
	 * Indicates which model is the target of the reference. If model A 
	 * references model B, then B is the target.
	 * @var Schema
	 */
	private $target;
	
	/**
	 * Creates a new reference between two models, this allows them to access
	 * data through the ORM, therefore not requiring additional user generated
	 * queries to retrieve data.
	 * 
	 * @param Schema|string $target
	 */
	public function __construct($target) {
		$this->target = $target;
	}
	
	/**
	 * Defines the target model, this model is the one 'donating' it's 
	 * primary key to the source so source can reference it as a parent
	 * model.
	 * 
	 * @param Schema $target
	 */
	public function setTarget(Schema$target) {
		$this->target = $target;
	}
	
	/**
	 * Returns the target model (the parent model of the source). Which offers
	 * it's primary keys to the target so it can reference them.
	 * 
	 * @return Schema
	 */
	public function getTarget() {
		#If the target is actually a class name.
		if (is_string($this->target) && Strings::endsWith($this->target, 'Model')) {
			$this->target = trim(substr($this->target, 0,  0 - strlen('Model')), '\/');
		}
		
		#Check if the passed argument already is a model
		if ($this->target instanceof Schema) {
			return $this->target;
		} 
		elseif (strtolower($this->target) === strtolower($this->getModel()->getName())) {
			return $this->target = $this->getModel();
		}
		else {
			return $this->target = $this->getModel()->getTable()->getDb()->table($this->target)->getSchema();
		}
	}
	
	/**
	 * Returns a list of the fields this reference generates on the source
	 * Model. It copies the list of primary fields from the target model and
	 * prepends the role (separated by underscores) to the name of the field.
	 * 
	 * This function is just included for convenience. It just generates the
	 * data in a easy way to pick, so you cannot modify the way this is done
	 * or the fields below.
	 * 
	 * @return Field[]
	 */
	public function makePhysical() {
		$fields   = $this->getTarget()->getPrimary()->getFields()->toArray();
		$physical = Array();
		$_return  = Array();
		
		foreach ($fields as $field) {
			$children = $field->getPhysical();
			foreach ($children as $child)
				$physical[] = $child;
		}
		
		foreach ($physical as $remote_field) {
			$field = clone $remote_field;
			$field->setName($this->getName() . '_' . $field->getName());
			$field->setLogicalField($this);
			$field->setReferencedField($remote_field);
			$_return[] = $field;
		}
		
		return $_return;
	}

	/**
	 * Defines this field and all of it's children as reference fields.
	 * 
	 * @return string
	 */
	public function getDataType() {
		return Field::TYPE_REFERENCE;
	}

	public function getAdapter(Model $model) {
		return new ReferenceAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		$of    = $this->getTable()->getDb()->getObjectFactory();
		$query = $of->queryInstance($this->getTarget()->getTable());
		$query->setAliased(true);
		
		foreach ($this->getPhysical() as $field) {
			/*
			 * Get the field being referenced. Check if it is a valid reference value,
			 * if the field is null something went terribly wrong during assembly
			 */
			$referenced = $field->getReferencedField();
			
			if ($referenced === null) { 
				throw new PrivateException('Unexpected value. Refernced field is null', 1804031133); 
			}
			
			/*
			 * Generate a query field for the local and remote queries. The order
			 * of the queries is actually not relevant.
			 */
			$local  = $of->queryFieldInstance($parent->getQueryTable(), $field);
			$remote = $of->queryFieldInstance($query->getQueryTable(), $referenced);
			
			
			$query->where($local, $remote);
		}
		
		return Array($query);
	}

}
