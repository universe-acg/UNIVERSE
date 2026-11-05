<?php
/** Read-only demo coverage report. No user data or credentials are exported. */
require dirname(__DIR__).'/config/bootstrap.php';
$kernel=new App\Kernel($_SERVER['APP_ENV'],(bool)$_SERVER['APP_DEBUG']);$kernel->boot();
$em=$kernel->getContainer()->get('doctrine')->getManager();$connection=$em->getConnection();$counts=[];
foreach($em->getMetadataFactory()->getAllMetadata() as $meta){
    if($meta->isMappedSuperclass || $meta->isEmbeddedClass)continue;
    $table=$connection->getDatabasePlatform()->quoteIdentifier($meta->getTableName());
    $counts[$meta->getName()]=(int)$connection->fetchOne('SELECT COUNT(*) FROM '.$table);
}
ksort($counts);echo json_encode(['entities'=>count($counts),'counts'=>$counts,'empty_entities'=>array_keys(array_filter($counts,fn($count)=>$count===0))],JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
