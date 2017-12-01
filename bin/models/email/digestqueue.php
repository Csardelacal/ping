<?php namespace email;

use IntegerField;
use Reference;
use spitfire\Model;
use spitfire\storage\database\Schema;


/**
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class DigestQueueModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		$schema->notification = new Reference('notification');
		$schema->created      = new IntegerField(true);
		
		
		$schema->user         = new Reference('user'); 
		$schema->type         = new IntegerField(); // This is generally an 
		//unnecessary denormalization. But it makes the code less crazy
	}
	
	public function onbeforesave() {
		if ($this->created === null) { $this->created = time(); }
	}

}