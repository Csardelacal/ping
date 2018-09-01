<?php 

$return = isset($_GET['returnto']) && Strings::startsWith($_GET['returnto'], '/') && !Strings::startsWith($_GET['returnto'], '//');
current_context()->response->getHeaders()->redirect($return? $_GET['returnto'] : url('feed'));