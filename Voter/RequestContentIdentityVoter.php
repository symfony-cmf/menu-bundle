<?php
namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\NodeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This voter compares whether a key in the request is identical to the content
 * entry in the options array.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class RequestContentIdentityVoter implements CurrentItemVoterInterface
{
    private $requestKey;

    /**
     * @param string $requestKey The key to look up the content in the request
     */
    public function __construct($requestKey)
    {
        $this->requestKey = $requestKey;
    }

    /**
     * {@inheritDoc}
     */
    public function isCurrentItem(Request $request, array $options, NodeInterface $node = null)
    {
        if ($request->attributes->has($this->requestKey)
            && isset($options['content'])
            && null !== $options['content']
            && $request->attributes->get($this->requestKey) === $options['content']
        ) {
            return self::VOTE_YES;
        }

        return self::VOTE_ABSTAIN;
    }
}
