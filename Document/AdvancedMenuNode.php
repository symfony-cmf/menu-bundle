<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowInterface;

/**
 * This class represents an advanced menu node document
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class AdvancedMenuNode extends MenuNode implements PublishWorkflowInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var boolean
     */
    protected $publishable = true;

    /**
     * @var \DateTime
     */
    protected $publishStartDate;

    /**
     * @var \DateTime
     */
    protected $publishEndDate;

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

    /**
     * {@inheritDoc}
     */
    public function isPublishable()
    {
        return $this->publishable;
    }

    /**
     * Set the publishable workflow flag.
     *
     * @param boolean $publishable
     */
    public function setPublishable($publishable)
    {
        $this->publishable = $publishable;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * {@inheritDoc}
     */
    public function setPublishStartDate(\DateTime $date = null)
    {
        $this->publishStartDate = $date;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * {@inheritDoc}
     */
    public function setPublishEndDate(\DateTime $date = null)
    {
        $this->publishEndDate = $date;
    }
}
