<?php

/** @var App\Core\Router $router */

$router->group(['prefix' => '/'],
    function ($router) {
        $router->add('GET', '', 'Home\\HomeController@index');
    }
);

$router->group(['prefix' => '/examples'],
    function ($router) {
        $router->add('GET', 'session', 'ExampleController@session');
        $router->add('GET', 'view', 'ExampleController@index');
        $router->add('GET', 'insert', 'ExampleController@insert');
        $router->add('GET', 'insertMultiple', 'ExampleController@insertMultiple');
        $router->add('GET', 'select', 'ExampleController@select');
        $router->add('GET', 'update', 'ExampleController@update');
        $router->add('GET', 'delete', 'ExampleController@delete');
        $router->add('GET', 'count', 'ExampleController@count');
        $router->add('GET', 'exists', 'ExampleController@exists');
    }
);