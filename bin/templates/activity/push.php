<?php 

$return = isset($_GET['returnto']) && Strings::startsWith($_GET['returnto'], '/') && !Strings::startsWith($_GET['returnto'], '//');
$this->response->getHeaders()->redirect($return? $_GET['returnto'] : url('feed'));