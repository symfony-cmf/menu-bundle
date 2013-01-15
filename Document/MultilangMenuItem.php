<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * This class has been renamed to something more appropriate, MultilangMenuNode,
 * this class is therefore deprecated. Please change your implementation
 * to use MultilangMenuNode instead of MultilangMenuItem.
 *
 * @PHPCRODM\Document
 */
class MultilangMenuItem extends MultilangMenuNode
{
}
