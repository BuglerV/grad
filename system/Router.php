<?php

namespace App;

class Router extends Patterns\Singleton
{
    protected static $instance;

    protected $route;
    
    protected function __construct($routes = []){
        if(!$routes){
            $env = Env::i()->wall;
            $routes = Locator::i()->locate("config/routes.$env.php");
        }
        $this->setRoutes($routes);
    }
    
    public function setRoutes($routes){
        $this->data = $routes;
    }
    
    public function addRoute($route){
        $this->data[] = $route;
    }
    
    public function getRouteName(){
        return $this->route['name'];
    }
    
    public function dispatch($request = null){
        $request = $request ?? Request::i();
        foreach($this->data as $name => $route){
            if(!preg_match('#^'.$route['url'].'$#i',$request->getPathInfo(),$matches))
                continue;
            if(isset($route['methods']) AND !in_array($request->getMethod(),$route['methods']))
                continue;
            if(isset($route['ips']) AND !in_array($request->server->get('REMOTE_ADDR'),$route['ips']))
                continue;
            
            if(isset($route['ajax']) AND $route['ajax'] AND !$request->isXmlHttpRequest())
                continue;
            
            list($controller,$method) = explode('::',$route['controller']);

            if(!class_exists($controller))
                continue;

            $this->route = [
                'name' => $name,
                'controller' => $controller,
                'method' => $method,
                'arguments' => $matches
            ];
            break;
        }

        return true;
    }
    
    public function controller()
    {
        $route = $this->route;
        $controller = new $route['controller'];
        if(method_exists($controller,$route['method']) OR method_exists($controller,'__call')){
            $controller->{$route['method']}($route['arguments']??null);
        }
        else{
            $controller->indexAction($route['arguments']??null);
        }
        return true;
    }
}