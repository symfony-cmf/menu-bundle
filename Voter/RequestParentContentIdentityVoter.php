<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A variant of the RequestContentIdentityVoter that checks if the request
 * content is of a specific class and if so checks if the value returned by
 * *getParentDocument()* is identical to the content item in the menu items extras.
 *
 * Note that there is no check, you have to make sure the $childClass does
 * indeed have a getParentDocument method.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class RequestParentContentIdentityVoter implements VoterInterface
{
    /**
     * @var string The key to look up the content in the request attributes
     */
    private $requestKey;

    /**
     * @var string Class for content having a getParent method
     */
    private $childClass;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @param string $requestKey The key to look up the content in the request
     *                           attributes
     * @param string $childClass Fully qualified class name of the model class
     *                           the content in the request must have to
     *                           attempt calling getParentDocument on it.
     */
    public function __construct($requestKey, $childClass)
    {
        $this->requestKey = $requestKey;
        $this->childClass = $childClass;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item = null)
    {
        if (!$this->request) {
            return;
        }

        $content = $item->getExtra('content');

        if (null !== $content
            && $this->request->attributes->has($this->requestKey)
            && $this->request->attributes->get($this->requestKey) instanceof $this->childClass
            && $this->request->attributes->get($this->requestKey)->getParentDocument() === $content
        ) {
            return true;
        }

        return;
    }
}
