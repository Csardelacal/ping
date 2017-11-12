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
	}
	
	public function onbeforesave() {
		if ($this->created === null) { $this->created = time(); }
	}

}