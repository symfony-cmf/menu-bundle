<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\DependencyInjection;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class XmlSchemaTest extends BaseTestCase
{
    public function testSchema()
    {
        $h = $this->helper('DependencyInjection');
        $res = $h->validate(
            __DIR__.'/../../Resources/app/config/cmf_menu.xml',
            __DIR__.'/../../../Resources/config/schema/menu-1.0.xsd'
        );

        $this->assertTrue($res);
    }
}
