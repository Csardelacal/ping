<?php namespace media;

use spitfire\storage\database\Table;

class MissingThumbModel extends ThumbModel
{
	// Disable database access
	public function __construct(Table $table = null, $data = null)
	{
		$table = null;
		$data = null;
		parent::__construct($table, $data);
	}
	
	public function getEmbed()
	{
		// TODO: temporary styling until overhaul with vue and tailwind
		$style = 'font-weight: bold; font-style: italic; text-align: center;';
		$style.= 'border: 1px solid black; padding: 5px; font-size: 0.9em; margin: 5px';
		return '<div style="${style}">Media Error</div>';
	}
	
	public function getMediaEmbed()
	{
		return $this->getEmbed();
	}
}
