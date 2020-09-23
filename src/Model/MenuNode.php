<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Model;

use Symfony\Cmf\Bundle\CoreBundle\Model\ChildInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

/**
 * This is the standard CMF MenuNode implementation.
 *
 * Menu bundle specific additions:
 *
 * - Link type: Ability to explicitly specify the type of link
 * - Content aware: Either a route of document implementing
 *     RouteAware can be used to determine the link.
 *
 * Standard CMF features:
 *
 * - Translatable
 * - Publish Workflow
 */
class MenuNode extends MenuNodeBase implements
    TranslatableInterface,
    PublishTimePeriodInterface,
    PublishableInterface,
    MenuOptionsInterface,
    ChildInterface
{
    /**
     * Parent menu node.
     *
     * @var mixed
     */
    protected $parent;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Enum, values determined by ContentAwareFactory.
     *
     * @var string
     */
    protected $linkType;

    /**
     * The content this menu item points to.
     *
     * @var object
     */
    protected $content;

    /**
     * @var bool
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
     * {@inheritdoc}
     */
    public function setParentObject($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentObject()
    {
        return $this->parent;
    }

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
     * Return the content document associated with this menu node.
     *
     * @return object the content of this menu node
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content document associated with this menu node.
     *
     * NOTE: When using doctrine, the content must be mapped for doctrine and
     * be persisted or cascading be configured on the content field.
     *
     * @param object $content
     *
     * @return MenuNode - this instance
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        $options = parent::getOptions();

        return array_merge($options, [
            'linkType' => $this->linkType,
            'content' => $this->getContent(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishable()
    {
        return $this->publishable;
    }

    /**
     * Set the publishable workflow flag.
     *
     * @param bool $publishable
     */
    public function setPublishable($publishable)
    {
        $this->publishable = $publishable;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishStartDate(\DateTime $date = null)
    {
        $this->publishStartDate = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishEndDate(\DateTime $date = null)
    {
        $this->publishEndDate = $date;
    }

    /**
     * Get the link type.
     *
     * The link type is used to explicitly determine which of the uri, route
     * and content fields are used to determine the link which will bre
     * rendered for the menu item. If it is empty this will be determined
     * automatically.
     *
     * @return string
     */
    public function getLinkType()
    {
        return $this->linkType;
    }

    /**
     * @see getLinkType
     * @see ContentAwareFactory::$validLinkTypes
     *
     * Valid link types are defined in ContenentAwareFactory
     *
     * @param $linkType string - one of uri, route or content
     */
    public function setLinkType($linkType)
    {
        $this->linkType = $linkType;
    }
}
