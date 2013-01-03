<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Serializer;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem;
use Doctrine\ODM\PHPCR\DocumentManager;

/**
 * MenuItemNormalizer
 *
 * Converts MenuItem to an array and vice-versa.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class MenuItemNormalizer extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected $dm;
    protected $menuItemClass = 'Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem';

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize($object, $format = null)
    {
        $array = array(
            'id' => $object->getId(),
            'name' => $object->getName(),
            'label' => $object->getLabel(),
            'uri' => $object->getUri(),
            'route' => $object->getRoute(),
            'weak' => $object->getWeak(),
            'attributes' => $object->getAttributes(),
            'extras' => $object->getExtras(),
            'childrenAttributes' => $object->getChildrenAttributes(),
        );

        $meta = $this->dm->getClassMetadata(get_class($object));
        $contentId = $meta->getIdentifierValue($object);

        $array['content'] = $contentId;

        // export the children as well, unless explicitly disabled
        $array['children'] = array();
        if (null !== $object->getChildren()) {
            foreach ($object->getChildren() as $child) {
                $array['children'][] = $this->normalize($child);;
            }
        }

        return $array;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($object, $format = null)
    {
        if ($object instanceOf MenuItem) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null)
    {
        static $id = 0;

        $name = sprintf('%d-item', $id++);

        $menuItem = new MenuItem;
        $menuItem->setName($name);
        $menuItem->setLabel($this->getValue($data, 'label'));
        $menuItem->setUri($this->getValue($data, 'uri'));
        $menuItem->setRoute($this->getValue($data, 'route'));
        $menuItem->setWeak($this->getValue($data, 'weak'));
        $menuItem->setAttributes($this->getValue($data, 'attributes', array()));
        $menuItem->setExtras($this->getValue($data, 'extras', array()));
        $menuItem->setChildrenAttributes($this->getValue($data, 'childrenAttributes', array()));

        if (isset($data['content'])) {
            $contentDoc = $this->dm->find($this->menuItemClass, $data['content']);
            $menuItem->setContent($contentDoc);
        }

        if (isset($data['children'])) {
            foreach ($data['children'] as $dataChild) {
                $childItem = $this->denormalize($dataChild, $class, $format);
                $menuItem->addChild($childItem);
            }
        }

        return $menuItem;
    }

    protected function getValue($data, $field, $default = null)
    {
        if (isset($data[$field])) {
            return $data[$field];
        }

        return $default;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type == $this->menuItemClass) {
            return true;
        }

        return false;
    }
}
