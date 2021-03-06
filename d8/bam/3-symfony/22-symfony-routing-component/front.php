<?php
 
require_once __DIR__ . '/vendor/autoload.php';
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
 
$request = Request::createFromGlobals();
$routes = include __DIR__ . '/src/app.php';
 
$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

// Examples of what gets returned for particular URLs:
print_r($matcher->match('/bye'));
print_r('<br>');
print_r($matcher->match('/hello/Fabien'));
print_r('<br>');
print_r($matcher->match('/hello'));
print_r('<br>');
//$matcher->match('/not-found');

// How to generate URLs with the UrlGenerator:
$generator = new Routing\Generator\UrlGenerator($routes, $context);
echo $generator->generate('hello', array('name' => 'Fabien'));
print_r('<br>');
echo $generator->generate('hello', array('name' => 'Fabien'), true); // Absolute URL
 
try {
  extract($matcher->match($request->getPathInfo()), EXTR_SKIP);
  ob_start();
  include sprintf(__DIR__.'/src/pages/%s.php', $_route);
  $response = new Response(ob_get_clean());
} catch (Routing\Exception\ResourceNotFoundException $e) {
  $response = new Response('Not Found', 404);
} catch (Exception $e) {
  $response = new Response('An error occurred', 500);
}
 
$response->send();