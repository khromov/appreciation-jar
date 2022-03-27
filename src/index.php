<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

$config = require '../config.php';

if (class_exists('PDO')) {
    if (!in_array("sqlite", PDO::getAvailableDrivers())) {
        echo "You need PDO + sqlite connector to use this software.";
        die();
    }
}

// Change this if you move the project into a subdirectory
$baseFolder = $config['baseFolder'];

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
    $db = \Khromov\AppreciationJar\Lib\Db::initDb();

    $lastId = false;
    
    $appreciation = $form['appreciation'] ?? null;
    if(is_string($appreciation) && trim($appreciation) !== '') {
        $trimmed_appreciation = trim($appreciation);
            
        $params = [time(), $trimmed_appreciation, ''];

        // Prepare and execute the SQL statement
        $stmt = $db->prepare('INSERT INTO appreciations(time, text, author) VALUES(?, ?, ?);');
        $stmt->execute($params);
        $lastId = $db->lastInsertId();

        $saved = $lastId ? true : false;
    } else {
        $saved = false;
    }

    return $renderer->render($response, "appreciate.php", ['saved' => $saved, 'baseFolder' => $baseFolder, 'id' => $lastId]);
});

$app->get('/admin/{secret}', function (Request $request, Response $response, array $args) use ($config, $renderer, $baseFolder) {
    $db = \Khromov\AppreciationJar\Lib\Db::initDb();

    $secret = $args['secret'] ?? null;

    if($secret && $secret === $config['secret']) {
        // Prepare and execute the SQL statement
        $stmt = $db->prepare("SELECT * FROM appreciations");
        $stmt->execute();
        
        // Get the results as an array with column names as array keys
        $appreciations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $renderer->render($response, "admin.php", ['baseFolder' => $baseFolder, 'appreciations' => $appreciations, 'secret' => $secret]);
    } else {
        return $renderer->render($response, "appreciate.php", ['saved' => false, 'baseFolder' => $baseFolder]);
    }
});

$app->post('/admin/delete/{id}', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder, $config) {
    $db = \Khromov\AppreciationJar\Lib\Db::initDb();
    $form = $request->getParsedBody();
    $secret = $form['secret'] ?? null;


    if($secret && $secret === $config['secret']) {
        // Prepare and execute the SQL statement
        $stmt = $db->prepare("DELETE FROM appreciations WHERE id = ?");
        $stmt->execute([intval($args['id'])]);

        return $response
        ->withHeader('Location', "${baseFolder}admin/{$secret}")
        ->withStatus(302);
    } else { // Error page
        return $renderer->render($response, "appreciate.php", ['saved' => false, 'baseFolder' => $baseFolder]);
    }
});

$app->get('/appreciation/{id}', function(Request $request, Response $response, array $args) {
    $db = \Khromov\AppreciationJar\Lib\Db::initDb();

    $id = $args['id'] ?? 0;

    // Prepare and execute the SQL statement
    $stmt = $db->prepare("SELECT * FROM appreciations WHERE id = ?");
    $stmt->execute([intval($id)]);
    $appreciation = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(sizeof($appreciation) === 0) {
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
    } else {
        $response->getBody()->write(json_encode($appreciation[0]));
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }
});

$app->run();