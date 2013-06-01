<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\Document;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Document\MultilangMenuNode;
use Symfony\Cmf\Component\Testing\Document\Content;
use Doctrine\ODM\PHPCR\Document\Generic;

class MultilangMenuNodeTest extends MenuNodeTest
{
    protected function getNewInstance()
    {
        return new MultilangMenuNode;
    }

    public function testMenuNode()
    {
        parent::testMenuNode();

        $menuNode = $this->dm->find(null, '/test/test-node');
        $menuNode->setLocale('fr');
        $this->dm->persist($menuNode);
        $this->dm->flush();
        $this->dm->clear();

        $menuNode = $this->dm->find(null, '/test/test-node');
        $this->assertEquals('fr', $menuNode->getLocale());
    }
}
