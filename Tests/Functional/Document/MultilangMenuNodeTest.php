<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\Model;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MultilangMenuNode;
use Symfony\Cmf\Component\Testing\Document\Content;
use Doctrine\ODM\PHPCR\Model\Generic;

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

        $menuNode = $this->dm->findTranslation(null, '/test/test-node', 'fr');
        $this->assertEquals('fr', $menuNode->getLocale());
    }
}
