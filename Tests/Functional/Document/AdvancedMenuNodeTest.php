<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Document;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Document\AdvancedMenuNode;
use Symfony\Cmf\Component\Testing\Document\Content;
use Doctrine\ODM\PHPCR\Document\Generic;

class AdvancedMenuNodeTest extends MenuNodeTest
{
    protected function getNewInstance()
    {
        return new AdvancedMenuNode;
    }

    protected function getMenuNodeData()
    {
        $data = parent::getMenuNodeData();
        
        $data['publishable'] = false;
        $data['publishStartDate'] = new \DateTime('2013-06-18');
        $data['publishEndDate'] = new \DateTime('2013-06-18');

        return $data;
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

        // test publish start and end
        $data = $this->getMenuNodeData();
        $publishStartDate = $data['publishStartDate'];
        $publishEndDate = $data['publishEndDate'];

        $this->assertInstanceOf('\DateTime', $publishStartDate);
        $this->assertInstanceOf('\DateTime', $publishEndDate);
        $this->assertEquals($data['publishStartDate']->format('Y-m-d'), $publishStartDate->format('Y-m-d'));
        $this->assertEquals($data['publishEndDate']->format('Y-m-d'), $publishEndDate->format('Y-m-d'));
    }
}
