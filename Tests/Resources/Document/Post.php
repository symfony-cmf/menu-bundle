<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

use Knp\Menu\NodeInterface;

use Symfony\Component\Routing\Route;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 */
class Post extends Content
{
}
