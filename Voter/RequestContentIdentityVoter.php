<?php
namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This voter compares whether a key in the request is identical to the content
 * entry in the options array.
 *
 * This voter is NOT enabled by default, as usually this is already covered
 * by the core menu bundle looking at request URLs.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class RequestContentIdentityVoter implements VoterInterface
{
    private $requestKey;
    private $request;

    /**
     * @param string $requestKey The key to look up the content in the request
     */
    public function __construct($requestKey)
    {
        $this->requestKey = $requestKey;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function matchItem(ItemInterface $item = null)
    {
        $options = $item->getAttributes();

        if ($this->request->attributes->has($this->requestKey)
            && isset($options['content'])
            && null !== $options['content']
            && $this->request->attributes->get($this->requestKey) === $options['content']
        ) {
            return true;
        }

        return null;
    }
}
