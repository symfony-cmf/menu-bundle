<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * This class represents a multilanguage menu item for the cmf.
 *
 * The label and uri are translatable, to have a language specific menu caption
 * and to be able to have external links language specific.
 *
 * To protect against accidentally injecting things into the tree, all menu
 * item node names must end on -item.
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
     * @return string the loaded locale of this menu item
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale this menu item should be. When doing a flush,
     * this will have the translated fields be stored as that locale.
     *
     * @param string $locale the locale to use for this menu item
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
