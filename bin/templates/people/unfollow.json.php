<?php

$data = json_encode(Array(
	 'following' => false
));

echo isset($_GET['p'])? sprintf('%s(%s)', $_GET['p'], $data) : $data;
