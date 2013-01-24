<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * This class represents a menu node for the cmf.
 *
 * @author Uwe Jäger <uwej711@googlemail.com>
 * @author Daniel Leech <daniel@dantleech.com>
 *
 * @PHPCRODM\Document
 */
class MenuNode extends AbstractMenuNode
{
}
