<?php
/** Verify every tracked demo JSON input before a seed or deployment. */
$root=dirname(__DIR__);
$files=glob($root.'/data/deree/*.json');
foreach($files as $file) {
    try { json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR); }
    catch(JsonException $error) { fwrite(STDERR,basename($file).': '.$error->getMessage().PHP_EOL); exit(1); }
}
echo count($files).' Deree JSON inputs are valid.',PHP_EOL;

// Validate demo-images.json references
$imageMap=$root.'/data/deree/demo-images.json';
if(is_file($imageMap)) {
    $map=json_decode(file_get_contents($imageMap),true,512,JSON_THROW_ON_ERROR);
    $missing=0;
    foreach(['venues','facilities'] as $section) {
        foreach($map[$section]??[] as $name=>$entry) {
            $file=$root.'/data/demo-media/'.$entry['file'];
            if(!is_file($file)){fwrite(STDERR,'Missing image: '.$entry['file'].' ('.$name.')'.PHP_EOL);$missing++;}
        }
    }
    foreach($map['event_categories']??[] as $category=>$images) {
        foreach($images as $entry) {
            $file=$root.'/data/demo-media/'.$entry['file'];
            if(!is_file($file)){fwrite(STDERR,'Missing event image: '.$entry['file'].' ('.$category.')'.PHP_EOL);$missing++;}
        }
    }
    // Validate club logos exist for all directory.json organizations
    $directory=$root.'/data/deree/directory.json';
    if(is_file($directory)) {
        $rows=json_decode(file_get_contents($directory),true,512,JSON_THROW_ON_ERROR);
        foreach($rows as $row) {
            if(!in_array($row['kind'],['club','society','organization']))continue;
            $slug=trim(preg_replace('/[^a-z0-9]+/','-',strtolower(trim($row['name']))),'-');
            $logo=$root.'/data/demo-media/clubs/'.$slug.'.png';
            if(!is_file($logo)){fwrite(STDERR,'Missing club logo: clubs/'.$slug.'.png ('.$row['name'].')'.PHP_EOL);$missing++;}
        }
    }
    if($missing){fwrite(STDERR,$missing.' demo image(s) missing. Run: php bin/console app:demo:download-images'.PHP_EOL);exit(1);}
    echo 'Demo image mapping verified: all referenced files exist.',PHP_EOL;
}
