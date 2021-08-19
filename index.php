<?php
// starten van de session
session_start(); 

ini_set('display_errors', true);
error_reporting(E_ALL);

$routes = array(
  'home' => array(
    'controller' => 'Pages',
    'action' => 'index'
  ),
  'popular' => array(
    'controller' => 'Pages', 
    'action' => 'popular'
  ),
  'detail' => array(
    'controller' => 'Pages', 
    'action' => 'detail'
  ), 
  'search' => array(
    'controller' => 'Pages', 
    'action' => 'search'
  ), 
  'upload' => array(
    'controller' => 'Pages', 
    'action' => 'upload'
  ),
);

if (empty($_GET['page'])) {
  $_GET['page'] = 'home';
}
if (empty($routes[$_GET['page']])) {
  header('Location: index.php');
  exit();
}

$route = $routes[$_GET['page']];
$controllerName = $route['controller'] . 'Controller';

require_once __DIR__ . '/controller/' . $controllerName . ".php";

$controllerObj = new $controllerName();
$controllerObj->route = $route;
$controllerObj->filter();
$controllerObj->render();
