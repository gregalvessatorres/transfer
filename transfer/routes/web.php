<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return 'you know, purely for academic purposes';
});

$router->group(['prefix' => 'transactions'], function () use ($router) {
    $router->post('', 'TransactionController@store');
    $router->get('', 'TransactionController@index');
    $router->get('{id}', 'TransactionController@show');
    $router->delete('{id}', 'TransactionController@destroy');
});

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('', 'UserController@store');
    $router->get('', ['as' => 'user_get', 'uses' => 'UserController@index']);
    $router->get('{id}', ['as' => 'user_get_id', 'uses' => 'UserController@show']);
    $router->delete('{id}', 'UserController@destroy');
});

$router->group(['prefix' => 'wallets'], function () use ($router) {
    $router->post('', 'WalletController@store');
    $router->get('', ['as' => 'wallet_get', 'uses' => 'WalletController@index']);
    $router->get('{id}', ['as' => 'wallet_get_id', 'uses' => 'WalletController@show']);
    $router->delete('{id}', 'WalletController@destroy');
});