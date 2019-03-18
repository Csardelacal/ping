<?php

echo json_encode(Array(
	'payload' => [
		'id' => $ping->_id,
		'guid' => $ping->guid
	]
));
