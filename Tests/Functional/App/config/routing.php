<?php

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection(
    $loader->import(CMF_TEST_CONFIG_DIR.'/routing/sonata_routing.yml'
));

return $collection;
