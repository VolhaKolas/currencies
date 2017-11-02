<?php
$router->get("", "CurrenciesController@get");
$router->post("currencies", "CurrenciesController@post");
$router->get("courses", "CoursesController@get");