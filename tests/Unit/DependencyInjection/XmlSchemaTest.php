<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\DependencyInjection;

use Symfony\Cmf\Component\Testing\Unit\XmlSchemaTestCase;

class XmlSchemaTest extends XmlSchemaTestCase
{
    public function testSchema()
    {
        $xmlFiles = array_map(function ($file) {
            return __DIR__.'/../../Fixtures/fixtures/config/'.$file;
        }, [
            'config1.xml',
            'config2.xml',
        ]);

        $this->assertSchemaAcceptsXml($xmlFiles, __DIR__.'/../../../src/Resources/config/schema/menu-1.0.xsd');
    }
}
