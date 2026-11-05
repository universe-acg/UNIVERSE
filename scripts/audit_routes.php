<?php
/** Non-mutating route/controller integrity check. Exit nonzero on unresolved handlers. */
require dirname(__DIR__).'/config/bootstrap.php';
$kernel=new App\Kernel($_SERVER['APP_ENV'],(bool)$_SERVER['APP_DEBUG']);$kernel->boot();
$routes=$kernel->getContainer()->get('router')->getRouteCollection();$errors=[];$count=0;
foreach($routes as $name=>$route){
    ++$count;$controller=$route->getDefault('_controller');
    if(!is_string($controller)||!str_starts_with($controller,'App\\')||!str_contains($controller,'::'))continue;
    [$class,$method]=explode('::',$controller,2);
    if(!class_exists($class)||!method_exists($class,$method)){$errors[]=['route'=>$name,'controller'=>$controller];continue;}
    foreach((new ReflectionMethod($class,$method))->getParameters() as $parameter){
        $type=$parameter->getType();
        if($type instanceof ReflectionNamedType && !$type->isBuiltin() && !class_exists($type->getName()) && !interface_exists($type->getName()))$errors[]=['route'=>$name,'unresolved_argument_type'=>$type->getName()];
    }
}
echo json_encode(['routes'=>$count,'unresolved_controllers'=>$errors],JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
exit($errors ? 1:0);
