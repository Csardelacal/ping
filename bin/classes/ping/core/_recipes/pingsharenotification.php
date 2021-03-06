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


$core->feed->push->after()->do(function ($ping) use ($core) {
		
	$original = $ping->share;
	
	while ($original) {
		/*
		 * If the user is not local to the server, the federation would be required
		 * to walk the chain and notify the application of a relevant ping.
		 */
		if ($original->src->server) { continue; } #The user is not local, the server on the other end shoudl take care

		$notification = db()->table('notification')->newRecord();
		$notification->src     = $ping->src;
		$notification->target  = $original->src->user;
		$notification->content = "shared your ping";
		$notification->type    = NotificationModel::TYPE_SHARE;
		$notification->url     = strval(url('ping', 'detail', $notification->_id)->absolute());

		$core->activity->push->do(function ($notification) {
			$notification->store();
		}, $notification);
		
		$original = $original->share;
	}
});