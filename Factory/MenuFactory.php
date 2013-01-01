<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Factory;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem;
use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory implements FactoryInterface
{
    public function createItem($name, array $options = array())
    {
        $item = new MenuItem($name, $this);

        $options = array_merge(array(
            'id' => null,
            'parent' => null,
            'name' => null,
            'label' => null,
            'uri' => null,
            'route' => null,
            'content' => null,
            'weak' => null,
            'attributes' => array(),
            'extras' => array(),
            'display' => null,
            'childrenAttributes' => array(),
        ), $options);

        $item
            ->setId($options['id'])
            ->setParent($options['parent'])
            ->setName($options['name'])
            ->setLabel($options['label'])
            ->setUri($options['uri'])
            ->setRoute($options['route'])
            ->setContent($options['content'])
            ->setWeak($options['weak'])
            ->setAttributes($options['attributes'])
            ->setExtras($options['extras'])
            ->setDisplay($options['display'])
            ->setChildrenAttributes($options['childrenAttributes']);

        return $item;
    }

    /**
     * Create a menu item from a NodeInterface
     *
     * @param NodeInterface $node
     * @return MenuItem
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = $this->createItem($node->getName(), $node->getOptions());

        foreach ($node->getChildren() as $childNode) {
            $item->addChild($this->createFromNode($childNode));
        }

        return $item;
    }

    /**
     * Creates a new menu item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from MenuItem->toArray().
     *
     * @param  array $data The array of data to use as a source for the menu tree
     * @param  string $name The name of the source (if not set in data['name'])
     * @return MenuItem
     */
    public function createFromArray(array $data, $name = null)
    {
        $name = isset($data['name']) ? $data['name'] : $name;
        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = array();
        }

        $item = $this->createItem($name, $data);
        foreach ($children as $name => $child) {
            $item->addChild($this->createFromArray($child, $name));
        }

        return $item;
    }
}

