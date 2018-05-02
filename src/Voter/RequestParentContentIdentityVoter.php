<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @param string            $requestKey   The key to look up the content in the request
     *                                        attributes
     * @param string            $childClass   Fully qualified class name of the model class
     *                                        the content in the request must have to
     *                                        attempt calling getParentDocument on it
     * @param RequestStack|null $requestStack
     */
    public function __construct($requestKey, $childClass, RequestStack $requestStack = null)
    {
        $this->requestKey = $requestKey;
        $this->childClass = $childClass;
        $this->requestStack = $requestStack;
    }

    /**
     * @deprecated since version 2.2. Pass a RequestStack to the constructor instead.
     */
    public function setRequest(Request $request = null)
    {
        @trigger_error(
            sprintf(
                'The %s() method is deprecated since version 2.2.
                Pass a Symfony\Component\HttpFoundation\RequestStack
                in the constructor instead.',
                __METHOD__),
            E_USER_DEPRECATED
        );

        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item)
    {
        $request = $this->getRequest();
        if (!$request) {
            return;
        }

        $content = $item->getExtra('content');

        if (null !== $content
            && $request->attributes->has($this->requestKey)
            && $request->attributes->get($this->requestKey) instanceof $this->childClass
            && $request->attributes->get($this->requestKey)->getParentDocument() === $content
        ) {
            return true;
        }
    }

    private function getRequest()
    {
        if ($this->requestStack) {
            return $this->requestStack->getMasterRequest();
        }

        return $this->request;
    }
}
