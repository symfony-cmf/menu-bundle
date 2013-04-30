<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\NodeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A variant of the RequestContentIdentityVoter that checks if the request
 * content is of a specific class and if so checks if the value returned by
 * *getParent()* is identical to the content item in the options array.
 *
 * Note that there is no check, you have to make sure the $childClass does
 * indeed have a getParent method.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class RequestParentContentIdentityVoter implements CurrentItemVoterInterface
{
    private $requestKey;
    private $childClass;

    /**
     * @param string $requestKey The key to look up the content in the request
     *                           attributes
     * @param string $childClass Fully qualified class name of the model class
     *                           the content in the request must have to
     *                           attempt calling getParent on it.
     */
    public function __construct($requestKey, $childClass)
    {
        $this->requestKey = $requestKey;
        $this->childClass = $childClass;
    }

    /**
     * {@inheritDoc}
     */
    public function isCurrentItem(Request $request, array $options, NodeInterface $node = null)
    {
        if ($request->attributes->has($this->requestKey)
            && isset($options['content'])
            && $request->attributes->get($this->requestKey) instanceof $this->childClass
            && $request->attributes->get($this->requestKey)->getParent() === $options['content']
        ) {
            return self::VOTE_YES;
        }

        return self::VOTE_ABSTAIN;
    }
}
