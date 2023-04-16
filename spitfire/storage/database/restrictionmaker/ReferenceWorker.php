<?php namespace spitfire\storage\database\restrictionmaker;

use Reference;
use spitfire\Model;
use spitfire\storage\database\RestrictionGroup;

/* 
 * The MIT License
 *
 * Copyright 2017 César de la Cal Bretschneider <cesar@magic3w.com>.
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

class ReferenceWorker implements WorkerInterface
{
	
	/**
	 * 
	 * @param RestrictionGroup  $parent
	 * @param string $field
	 * @param string $operator
	 * @param Model  $value
	 */
	public function make(RestrictionGroup$parent, $field, $operator, $value) {
		if (!($value instanceof Model)) { return false; }
		if (is_string($field)) { $field = $parent->getQuery()->getTable()->getSchema()->getField($field); }
		
		/*
		 * If no field was found, then we do not need to continue either. Since it 
		 * implies that the worker is not gonna work here either.
		 */
		if (!$field instanceof Reference) { return false; }
		
		/*
		 * Prepare the resources we need to assemble the appropriate restriction 
		 * group to create the "sub-restrictions"
		 */
		$of       = $parent->getQuery()->getTable()->getDb()->getObjectFactory();
		$physical = $field->getPhysical();
		$restr    = $of->restrictionGroupInstance($parent, RestrictionGroup::TYPE_AND);
		$primary  = $value->getPrimaryData();
		
		/*
		 * Create the appropriate restrictions for this.
		 */
		foreach ($physical as $f) {
			$restr->add([
				$of->restrictionInstance($restr, $of->queryFieldInstance($parent->getQuery()->getQueryTable(), $f), 
				$primary[$f->getReferencedField()->getName()], 
				$operator
			)]);
		}
		
		return $restr;
	}

}
