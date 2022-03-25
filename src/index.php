<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

if (class_exists('PDO')) {
    if (!in_array("sqlite", PDO::getAvailableDrivers())) {
        echo "You need PDO + sqlite connector to use this software.";
        die();
    }
}

// Change this if you move the project into a subdirectory
$baseFolder = '/';

$app = AppFactory::create();

$renderer = new PhpRenderer('./templates');

$app->get('/', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder) {
    $adverbs = ['how', 'when', 'that'];
    $randomAdverb = $adverbs[rand(0, count($adverbs)-1)];
    return $renderer->render($response, "form.php", ['adverb' => $randomAdverb]);
});

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

$app->get('/init', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder) {
    $fileName = __DIR__ . "/../db/appreciations.sqlite";
    $dsn = "sqlite:$fileName";

    try {
        $db = new PDO($dsn);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Failed to connect to the database using DSN:<br>$dsn<br>";
        throw $e;
    }
    
    // Prepare and execute the SQL statement
    $stmt = $db->prepare("SELECT * FROM appreciations");
    $stmt->execute();
    
    // Get the results as an array with column names as array keys
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>", print_r($res, true), "</pre>";

    $response->getBody()->write('OK');
    return $response;
});

$app->run();