<?php

/* 
 * The MIT License
 *
 * Copyright 2020 César de la Cal Bretschneider <cesar@magic3w.com>.
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

current_context()->response->getHeaders()->contentType('json');

$settings = [
	'type' => 'switches.setting.definition',
	'definitions' => [
		#General settings
		['key' => sprintf('app%s', $appid), 'type' => 'group', 'caption' => $name, 'parent' => null, 'icon' => $icon->xl],
		[
			'key' => sprintf('app%s.display.sensitive', $appid), 
			'parent' => sprintf('app%s', $appid),
			'type' => 'setting:enum', 
			'caption' => ['*' => 'Sensitive content'], 
			'description' => ['*' => 'Indicates whether you would like to see potentially sensitive or disturbing content'], 
			'additional' => ['false' => 'Ask before showing sensitive content', 'true' => 'Show sensitive content']
		],
		#Notification settings
		['key' => sprintf('app%s.notifications', $appid), 'type' => 'group', 'caption' => 'Notification settings', 'parent' => sprintf('app%s', $appid), 'icon' => null],
	]
];

/*
 * 
 */
foreach (NotificationModel::getTypesAvailable() as $readable => $notification) {
	$settings ['definitions'][] = [
		'key' => sprintf('app%s.notifications.type%s.email', $appid, $notification),
		'parent' => sprintf('app%s.notifications', $appid),
		'type'   => 'setting:enum',
		'caption' => [ '*' => 'Notifications: ' . $readable ],
		'description' => ['*' => 'Would you like to receive these notifications via email, or just on site'],
		'additional'  => ['email' => 'Immediate email notifications', 'digest' => 'Only once a day', 'none' => 'Do not send emails']
	];
}

echo json_encode($settings);