<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Serializer;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem;
use Doctrine\ODM\PHPCR\DocumentManager;

class MenuItemNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
{
    protected $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

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

    public function supportsNormalization($object, $format = null)
    {
        if ($object instanceOf MenuItem) {
            return true;
        }

        return false;
    }
}
