<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\NonBufferedBody;
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

if($baseFolder !== '') {
    $app->setBasePath($baseFolder);
}

$renderer = new PhpRenderer('./templates');

$app->get('/', function (Request $request, Response $response, array $args) use ($renderer, $baseFolder) {
    $adverbs = ['how', 'when', 'that'];
    $randomAdverb = $adverbs[rand(0, count($adverbs)-1)];
    return $renderer->render($response, "form.php", ['adverb' => $randomAdverb, 'baseFolder' => $baseFolder]);
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

$app->post('/api/like', function (Request $request, Response $response, array $args) use ($db, $config, $renderer, $baseFolder) {
    $form = $request->getParsedBody();
    
    $appreciationId = isset($form['appreciationId']) ? intval($form['appreciationId']) : 0;

    $appreciation = Db::getAppreciation($appreciationId);

    if(!$appreciation) {
        $response->getBody()->write(json_encode(new stdClass)); // Return {}

        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
    } else {
        Db::addLike($appreciationId);
        $response->getBody()->write(json_encode([ 'likes' => Db::getLikes($appreciationId) ]));
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }
});


$app->get('/latest', function (Request $request, Response $response, array $args) use ($db, $config, $renderer, $baseFolder) {
    $appreciation = Db::getLatestAppreciation();

    if($appreciation) {
        return $renderer->render($response, "latest.php", ['appreciation' => Helpers::enrichAppreciation($appreciation), 'baseFolder' => $baseFolder, 'latest' => true]);
    } else {
        return $renderer->render($response, "error.php", [ 'errorMessage' => 'Could not find the appreciation, maybe it was deleted?', 'baseFolder' => $baseFolder]);
    }
});

$app->get('/archive', function (Request $request, Response $response, array $args) use ($db, $config, $renderer, $baseFolder) {
    $timeAgo = new Westsworld\TimeAgo();
    $appreciations = Db::getVisibleAppreciations();

    $appreciations = array_map(function($appreciation) {
        return Helpers::enrichAppreciation($appreciation);
    }, $appreciations);
    
    if(sizeof($appreciations) > 0) {
        return $renderer->render($response, "archive.php", ['appreciations' => $appreciations, 'baseFolder' => $baseFolder]);
    } else {
        return $renderer->render($response, "error.php", [ 'errorMessage' => 'Could not find any appreciations.', 'baseFolder' => $baseFolder]);
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
        
        $appreciations = $stmt->fetchAll();

        return $renderer->render($response, "admin.php", ['baseFolder' => $baseFolder, 'appreciations' => $appreciations, 'secret' => $secret, 'currentlyPublished' => Db::getLatestAppreciationId()]);
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
        ->withHeader('Location', "${baseFolder}/admin/{$secret}")
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
$app->post('/increment', function(Request $request, Response $response, array $args) use($db) {
    Db::maybeIncrementLatestAppreciationId(true);
    $newCount = intval(Db::getMetadata('latestAppreciation', 0));

    $response->getBody()->write("OK - latest appreciation: " . json_encode($newCount));
    return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
});

// https://discourse.slimframework.com/t/implementing-server-sent-events-with-slim-4/4482/5
$app->get('/events', function (Request $request, Response $response) {
    $response = $response
        ->withBody(new NonBufferedBody())
        ->withHeader('Content-Type', 'text/event-stream')
        ->withHeader('Cache-Control', 'no-cache')
        ->withHeader('X-Accel-Buffering', 'no'); // https://www.nginx.com/resources/wiki/start/topics/examples/x-accel/

    $body = $response->getBody();

    // 1 is always true, so repeat the while loop forever (aka event-loop)
    while (1) {
        $latestAppreciationId = Db::getLatestAppreciationId();
        $likes = Db::getLikes($latestAppreciationId);
        // Send named event
        $now = date('Y-m-d H:i:s');
        $event = sprintf("event: %s\ndata: %s\n\n", 'ping', json_encode(['latest' => $latestAppreciationId, 'likes' => $likes]));

        // Add a whitespace to the end
        $body->write($event . ' ');

        // break the loop if the client aborted the connection (closed the page)
        // https://discourse.slimframework.com/t/implementing-server-sent-events-with-slim-4/4482/9
        if (connection_aborted()) { 
            break;
        }

        // sleep for 1 second before running the loop again
        sleep(2);
    }

    return $response;
});

$app->run();