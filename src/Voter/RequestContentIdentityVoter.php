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

/**
 * This voter compares whether a key in the request is identical to the content
 * entry in the menu item extras.
 *
 * This voter is NOT enabled by default, as usually this is already covered
 * by the core menu bundle looking at request URLs.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class RequestContentIdentityVoter implements VoterInterface
{
    /**
     * @var string The key to look up the content in the request attributes
     */
    private $requestKey;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @param string $requestKey The key to look up the content in the request
     *                           attributes
     */
    public function __construct($requestKey)
    {
        $this->requestKey = $requestKey;
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
            && $this->request->attributes->get($this->requestKey) === $content
        ) {
            return true;
        }
    }
}
