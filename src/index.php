<?php
 // PHP permanent URL redirection
 header("Location: http://{$_SERVER['SERVER_NAME']}".str_replace([$_SERVER['QUERY_STRING'], '?'], '', $_SERVER['REQUEST_URI'])."web/", true, 303);
 exit();