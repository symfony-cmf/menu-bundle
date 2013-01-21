<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * This class represents a multilanguage menu node for the cmf.
 *
 * The label and uri are translatable, to have a language specific menu caption
 * and to be able to have external links language specific.
 *
 * @PHPCRODM\Document(translator="attribute")
 */
class MultilangMenuNode extends MenuNode
{

    /** @PHPCRODM\Locale */
    protected $locale;

    /** @PHPCRODM\String(translated=true) */
    protected $label = '';

    /** @PHPCRODM\Uri(translated=true) */
    protected $uri;

    /**
     * @return string the loaded locale of this menu node
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale this menu node should be. When doing a flush,
     * this will have the translated fields be stored as that locale.
     *
     * @param string $locale the locale to use for this menu node
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
