<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

$treeBrowserVersion = '1.x';
if (class_exists('Symfony\Cmf\Bundle\ResourceRestBundle\CmfResourceRestBundle')) {
    $treeBrowserVersion = '2.x';
}

$collection->addCollection($loader->import(__DIR__.'/routing/sonata.yml'));
$collection->addCollection($loader->import(__DIR__.'/routing/tree_browser_'.$treeBrowserVersion.'.yml'));
$collection->addCollection($loader->import(__DIR__.'/routing/test.yml'));

return $collection;
