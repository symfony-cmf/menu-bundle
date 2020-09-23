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
use Symfony\Component\Routing\Route;

/**
 * This voter checks if the content entry in the menu item extras is a Symfony
 * Route instance and if so compares its option "currentUriPrefix" with the
 * request path. This allows to configure a menu entry to be the current entry
 * for a whole sub path.
 *
 * This voter is NOT enabled by default. Enable it in your bundle configuration
 * and set up a currentUriPrefix on menu content that is routes.
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 * @author David Buchmann <mail@davidbu.ch>
 */
class UriPrefixVoter implements VoterInterface
{
    /**
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @deprecated Use the request stack instead
     *
     * @var Request|null
     */
    private $request;

    public function __construct(RequestStack $requestStack = null)
    {
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
    public function matchItem(ItemInterface $item): ?bool
    {
        $request = $this->getRequest();
        if (!$request) {
            return false;
        }

        $content = $item->getExtra('content');

        if ($content instanceof Route && $content->hasOption('currentUriPrefix')) {
            $currentUriPrefix = $content->getOption('currentUriPrefix');
            $currentUriPrefix = str_replace('{_locale}', $request->getLocale(), $currentUriPrefix);
            if (0 === strncmp($request->getPathInfo(), $currentUriPrefix, strlen($currentUriPrefix))) {
                return true;
            }
        }

        return null;
    }

    private function getRequest()
    {
        if ($this->requestStack) {
            return $this->requestStack->getMasterRequest();
        }

        return $this->request;
    }
}
