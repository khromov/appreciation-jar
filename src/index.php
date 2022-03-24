<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

// Change this if you move the project into a subdirectory
$baseFolder = '/';

$app = AppFactory::create();

$renderer = new PhpRenderer('./templates');

$app->get('/', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder) {
    return $renderer->render($response, "index.php", $args);
});

// TODO: Just create an error page
$app->get('/appreciate', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder) {
    return $renderer->render($response, "appreciate.php", ['saved' => false, 'baseFolder' => $baseFolder]);
});

$app->post('/appreciate', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder) {
    $form = $request->getParsedBody();
    
    $appreciation = $form['appreciation'] ?? null;
    // Save in db

    $saved = $appreciation ? true : false;

    return $renderer->render($response, "appreciate.php", ['saved' => $saved, 'baseFolder' => $baseFolder]);
});

$app->run();