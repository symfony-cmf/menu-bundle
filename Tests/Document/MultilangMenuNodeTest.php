<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Document;
use Symfony\Cmf\Bundle\MenuBundle\Document\MultilangMenuNode;

class MultilangMenuNodeTest extends \PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        $this->node = new MultilangMenuNode;;
    }

    public function testGetSetLocale()
    {
        $this->node->setLocale('fr');
        $this->assertEquals('fr', $this->node->getLocale());
    }
}
