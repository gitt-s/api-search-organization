<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
error_reporting(-1);

require '../vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,

        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];

$app = new \Slim\App([$config]);

header("Content-Type:text/html;charset=UTF-8");

$app->post( '/', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $token = $data['token'];
    $name = $data['name'];
    $city = $data['city'];
    $address = $data['address'];
    $country = $data['country'];
    $building = $data['building'];
    $service = $data['service'];


    $check = new CheckService($name, $city, $address, $country, $building, $service);

    try {
        new ValidData($name, $city, $address, $token);
        return $response->withStatus(200)->write($check->optionService());
    } catch (TokenException $e){
        return $response->withStatus(403)->write($e->getMessage());
    } catch (ValidException $e){
        return $response->withStatus(404)->write($e->getMessage());
    }

});


$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};


$app->run();