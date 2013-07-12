<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Document;
use Symfony\Cmf\Bundle\MenuBundle\Document\AdvancedMenuNode;

class AdvancedMenuNodeTest extends \PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        $this->node = new AdvancedMenuNode;;
    }

    public function testGetSetLocale()
    {
        $this->node->setLocale('fr');
        $this->assertEquals('fr', $this->node->getLocale());
    }

    public function testPublishWorkflowInterface()
    {
        $startDate = new \DateTime('2013-01-01');
        $endDate = new \DateTime('2013-02-01');

        $this->assertInstanceOf(
            'Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowInterface', 
            $this->node
        );

        // test defaults
        $this->assertTrue($this->node->isPublishable());
        $this->assertNull($this->node->getPublishStartDate());
        $this->assertNull($this->node->getPublishEndDate());

        $this->node->setPublishable(false);
        $this->node->setPublishStartDate($startDate);
        $this->node->setPublishEndDate($endDate);

        $this->assertSame($startDate, $this->node->getPublishStartDate());
        $this->assertSame($endDate, $this->node->getPublishEndDate());
    }

}
