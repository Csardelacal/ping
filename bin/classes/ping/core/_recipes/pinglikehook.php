<?php

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

$core->feedback->push->after()->do(function ($feedback) use ($core) {
		
	if ($feedback->ping->src->server) { return; } #The user is not local, the server on the other end shoudl take care
	
	#Check the current context for a hook instance
	$hook = current_context()->controller->hook;
	
	if (!$hook) { return; }
	
	$hook->trigger('ping.feedback', [
		'reaction' => $feedback->reaction,
		
		'to' => [
			'ping' => $feedback->ping->_id,
			'author' => $feedback->ping->src->_id, 
			'user' => $feedback->ping->src->user->_id
		],
		
		'by' => [
			'feedback' => $feedback->_id,
			'author' => $feedback->author->_id, 
			'user' => $feedback->author->user->_id
		]
	]);
});