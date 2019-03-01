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

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Document\Generic;
use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Tests\Fixtures\App\Document\Content;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuNodeTest extends BaseTestCase
{
    private $content;

    /**
     * @var DocumentManager
     */
    private $dm;

    private $rootDocument;

    /**
     * @var MenuNode
     */
    private $child1;

    protected function setUp()
    {
        $this->db('PHPCR')->createTestNode();

        $this->dm = $this->db('PHPCR')->getOm();
        $this->rootDocument = $this->dm->find(null, '/test');

        $this->content = new Content();
        $this->content->setId('/test/fake_weak_content');
        $this->content->setTitle('fake_weak_content');
        $this->dm->persist($this->content);

        $this->child1 = new MenuNode();
        $this->child1->setName('child1');
    }

    public function testMenuNode()
    {
        $data = [
            'name' => 'test-node',
            'label' => 'label_foobar',
            'uri' => 'http://www.example.com/foo',
            'route' => 'foo_route',
            'linkType' => 'route',
            'content' => $this->content,
            'publishable' => false,
            'publishStartDate' => new \DateTime('2013-06-18'),
            'publishEndDate' => new \DateTime('2013-06-18'),
            'attributes' => [
                'attr_foobar_1' => 'barfoo',
                'attr_foobar_2' => 'barfoo',
            ],
            'childrenAttributes' => [
                'child_foobar_1' => 'barfoo',
                'child_foobar_2' => 'barfoo',
            ],
            'linkAttributes' => [
                'link_foobar_1' => 'barfoo',
                'link_foobar_2' => 'barfoo',
            ],
            'labelAttributes' => [
                'label_foobar_1' => 'barfoo',
                'label_foobar_2' => 'barfoo',
            ],
            'extras' => [
                'extra_foobar_1' => 'barfoo',
                'extra_foobar_2' => 'barfoo',
            ],
            'routeParameters' => [
                'route_param_foobar_1' => 'barfoo',
                'route_param_foobar_2' => 'barfoo',
            ],
            'routeAbsolute' => true,
            'display' => false,
            'displayChildren' => false,
        ];

        $startDateString = $data['publishStartDate']->format('Y-m-d');
        $endDateString = $data['publishEndDate']->format('Y-m-d');

        $menuNode = new MenuNode();
        $refl = new \ReflectionClass($menuNode);

        $menuNode->setParentDocument($this->rootDocument);

        foreach ($data as $key => $value) {
            $refl = new \ReflectionClass($menuNode);
            $prop = $refl->getProperty($key);
            $prop->setAccessible(true);
            $prop->setValue($menuNode, $value);
        }

        $menuNode->addChild($this->child1);

        $this->dm->persist($menuNode);
        $this->dm->flush();
        $this->dm->clear();

        $menuNode = $this->dm->find(null, '/test/test-node');

        $this->assertNotNull($menuNode);

        foreach ($data as $key => $value) {
            $prop = $refl->getProperty($key);
            $prop->setAccessible(true);
            $v = $prop->getValue($menuNode);

            if (!\is_object($value)) {
                $this->assertEquals($value, $v);
            }
        }

        // test objects
        $prop = $refl->getProperty('content');
        $prop->setAccessible(true);
        $content = $prop->getValue($menuNode);
        $this->assertEquals('fake_weak_content', $content->getName());

        // test children
        $this->assertCount(1, $menuNode->getChildren());

        // test publish start and end
        $publishStartDate = $menuNode->getPublishStartDate();
        $publishEndDate = $menuNode->getPublishEndDate();

        $this->assertInstanceOf('\DateTime', $publishStartDate);
        $this->assertInstanceOf('\DateTime', $publishEndDate);
        $this->assertEquals($startDateString, $publishStartDate->format('Y-m-d'));
        $this->assertEquals($endDateString, $publishEndDate->format('Y-m-d'));

        // test multi-lang
        $menuNode->setLocale('fr');
        $this->dm->persist($menuNode);
        $this->dm->flush();
        $this->dm->clear();

        $menuNode = $this->dm->findTranslation(null, '/test/test-node', 'fr');
        $this->assertEquals('fr', $menuNode->getLocale());

        $child = $this->dm->find(null, '/test/test-node/child1');
        $menuNode = $child->getParentDocument();
        $this->assertCount(1, $menuNode->getChildren());
        $menuNode->removeChild($child);
        $this->dm->flush();
        $this->dm->clear();
        $menuNode = $this->dm->find(null, '/test/test-node');
        $this->assertCount(0, $menuNode->getChildren());
    }

    public function testPersistInvalidChild()
    {
        $this->expectException(\Doctrine\ODM\PHPCR\Exception\OutOfBoundsException::class);
        $this->expectExceptionMessage('Allowed child classes "Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\MenuNode"');

        $menuNode = new MenuNode();
        $menuNode->setName('menu-node');
        $menuNode->setParentDocument($this->rootDocument);
        $this->dm->persist($menuNode);

        $generic = new Generic();
        $generic->setParentDocument($menuNode);
        $generic->setNodename('invalid');
        $this->dm->persist($generic);

        $this->dm->flush();
    }
}
