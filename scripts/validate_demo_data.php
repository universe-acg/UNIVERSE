<?php
/** Verify every tracked demo JSON input before a seed or deployment. */
$files=glob(dirname(__DIR__).'/data/deree/*.json');
foreach($files as $file) {
    try { json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR); }
    catch(JsonException $error) { fwrite(STDERR,basename($file).': '.$error->getMessage().PHP_EOL); exit(1); }
}
echo count($files).' Deree JSON inputs are valid.',PHP_EOL;
