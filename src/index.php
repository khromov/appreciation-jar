<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Khromov\AppreciationJar\Lib\Helpers;
use Khromov\AppreciationJar\Lib\Db;

require __DIR__ . '/../vendor/autoload.php';

$config = Helpers::getConfig();
$db = Db::initialize();

Db::maybeIncrementLatestAppreciationId();

if (class_exists('PDO')) {
    if (!in_array("sqlite", PDO::getAvailableDrivers())) {
        echo "You need PDO + sqlite connector to use this software.";
        die();
    }
}

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

$app->post('/appreciate', function (Request $request, Response $response, array $args) use ($db, $config, $renderer, $baseFolder) {
    $form = $request->getParsedBody();

    $lastId = false;
    
    $appreciation = $form['appreciation'] ?? null;
    $name = $form['name'] ?? null;

    $allowedNames = array_map(fn($allowedName) => trim($allowedName), explode(',', $config['names']));

    if(in_array($name, $allowedNames) && is_string($appreciation) && trim($appreciation) !== '') {
        $trimmed_appreciation = trim($appreciation);
            
        $params = [time(), $trimmed_appreciation, $name];

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

$app->get('/latest', function (Request $request, Response $response, array $args) use ($db, $config, $renderer, $baseFolder) {
    $timeAgo = new Westsworld\TimeAgo();
    $appreciation = Db::getLatestAppreciation();

    if($appreciation) {
        $appreciationTime = DateTime::createFromFormat( 'U', $appreciation['time']);
        $appreciation['timeFormatted'] = $timeAgo->inWords($appreciationTime);

        return $renderer->render($response, "latest.php", ['appreciation' => $appreciation]);
    } else {
        return $renderer->render($response, "error.php", [ 'errorMessage' => 'Could not find the appreciation, maybe it was deleted?', 'baseFolder' => $baseFolder]);
    }
});

$app->get('/admin', function (Request $request, Response $response, array $args) use ($config, $renderer, $baseFolder) {
    return $renderer->render($response, "error.php", [ 'errorMessage' => 'Don\'t forget to add the password, like this: /admin/<password>', 'baseFolder' => $baseFolder]);
});

$app->get('/admin/{secret}', function (Request $request, Response $response, array $args) use ($db, $config, $renderer, $baseFolder) {
    $secret = $args['secret'] ?? null;

    if($secret && $secret === $config['secret']) {
        // Prepare and execute the SQL statement
        $stmt = $db->prepare("SELECT * FROM appreciations");
        $stmt->execute();
        
        // Get the results as an array with column names as array keys
        $appreciations = $stmt->fetchAll();

        return $renderer->render($response, "admin.php", ['baseFolder' => $baseFolder, 'appreciations' => $appreciations, 'secret' => $secret]);
    } else {
        return $renderer->render($response, "error.php", [ 'errorMessage' => '🤷‍♂️', 'baseFolder' => $baseFolder]);
    }
});

$app->post('/admin/delete/{id}', function (Request $request, Response $response, array $args) use ($db, $renderer, $baseFolder, $config) {
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

$app->get('/api/appreciation/{id}', function(Request $request, Response $response, array $args) use($db) {
    $id = $args['id'] ?? 0;

    if($id === 'latest') {
        $id = intval(Db::getMetadata('latestAppreciation', 0));
    }

    // Prepare and execute the SQL statement
    $stmt = $db->prepare("SELECT * FROM appreciations WHERE id = ?");
    $stmt->execute([intval($id)]);
    $appreciation = $stmt->fetch();

    if(!$appreciation) {
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
    } else {
        $timeAgo = new Westsworld\TimeAgo();
        $appreciationTime = DateTime::createFromFormat( 'U', $appreciation['time']);
        $appreciation['timeFormatted'] = $timeAgo->inWords($appreciationTime);

        $response->getBody()->write(json_encode($appreciation));
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }
});

// Test to increment values
$app->get('/increment', function(Request $request, Response $response, array $args) use($db) {
    Db::maybeIncrementLatestAppreciationId(true);
    $newCount = intval(Db::getMetadata('latestAppreciation', 0));

    $response->getBody()->write("OK - latest appreciation: " . json_encode($newCount));
    return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
});

$app->run();