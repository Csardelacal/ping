<?php

use spitfire\exceptions\FileNotFoundException;
use settings\NotificationModel as NotificationSetting;

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

/*
 * After a new activity is pushed to the database, the system should decide whether
 * it needs to send a email to the user immediately, later (as a digest) or not at all.
 */
$core->activity->push->after()->do(function ($parameter) {
	
	//TODO This code deserves to go, it's broken
	$sso     = new \auth\SSOCache(\spitfire\core\Environment::get('SSO'));
	$email   = new \EmailSender($sso);
	
	$target  = $parameter->target;
	$src     = $parameter->src;
	$url     = $parameter->url;
	$content = $parameter->content;
	$type    = $parameter->type;
	
	if ((!$target instanceof UserModel) || $target->notify($type, NotificationSetting::NOTIFY_EMAIL)) {
		//TODO This code deserves to go, it's broken
		$email->push($target->_id, $src, $content, $url);
	}
	elseif ($target->notify($type, NotificationSetting::NOTIFY_DIGEST)) {
		/*
		 * The user chose to receive digests of their notifications. Defer the sending 
		 * of the email to a later point in time.
		 */
		$r = db()->table('email\digestqueue')->newRecord();
		$r->user         = $target;
		$r->type         = $type;
		$r->notification = db()->table('notification')->get('_id', $parameter->id)->first();
		$r->store();
	}
});