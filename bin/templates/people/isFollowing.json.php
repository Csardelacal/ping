<?php

$data = json_encode(Array(
	 'error'     => isset($error) && $error,
	 'following' => !!$following
));

echo isset($_GET['p'])? sprintf('%s(%s)', $_GET['p'], $data) : $data;
