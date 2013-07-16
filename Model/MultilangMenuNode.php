<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Model;

/**
 * This class represents a multilanguage menu node for the cmf.
 *
 * The label and uri are translatable, to have a language specific menu caption
 * and to be able to have external links language specific.
 */
class MultilangMenuNode extends MenuNode
{
    /**
     * @var string
     */
    protected $locale;

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
