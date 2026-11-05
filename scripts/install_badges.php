<?php
/** Install the project's original badge art into its stable, local public URLs. */
(static function(): void {
    $root=dirname(__DIR__); $directory=$root.'/public/uploads/imgs';
    if (!is_dir($directory) && !mkdir($directory,0755,true) && !is_dir($directory)) throw new RuntimeException('Cannot create badge directory.');
    $files=['LEVELS_ATTENDEES-01.png','LEVELS_ATTENDEES-02.png','LEVELS_ATTENDEES-03.png','LEVELS_ATTENDEES-04.png','LEVELS_ATTENDEES-05-05.png','smurf.png','ricky.png','force.png','inter.png'];
    $mapping=array_combine($files,$files); $mapping['special.png']='LEVELS_ATTENDEES-06.png';
    foreach ($mapping as $targetName=>$sourceName) {
        $source=$root.'/assets/img/placeholders/'.$sourceName; $target=$directory.'/'.$targetName;
        if (!is_file($source)) throw new RuntimeException('Missing original badge artwork: '.$sourceName);
        if (!is_file($target) || hash_file('sha256',$target)!==hash_file('sha256',$source)) {
            if (!copy($source,$target)) throw new RuntimeException('Cannot install badge: '.$targetName);
            chmod($target,0644);
        }
    }
    echo 'Local participation badge assets are ready.',PHP_EOL;
})();
