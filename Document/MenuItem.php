<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * @deprecated Use the MenuNode instead.
 * 
 * This class has been renamed to something more appropriate.
 *
 * @PHPCRODM\Document
 */
class MenuItem extends MenuNode
{
}
