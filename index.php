<?php

require 'vendor/autoload.php';

use DTL\NotionApi\Notion;

try{
    $notion = new Notion('key9hoAsF8t2QCcwr', 'v1'); // version-default is 'v1'
} catch (Exception $e){
    echo $e->getMessage();
}

$rs = $notion->databases()->find('appOWwftb0qTloDhy');

print_r($rs);
