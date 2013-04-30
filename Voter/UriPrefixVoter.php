<?php
namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Knp\Menu\NodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * This voter checks if the content option is a Symfony Route instance and if
 * so compares its currentUriPrefix with the request path.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class UriPrefixVoter implements CurrentItemVoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function isCurrentItem(Request $request, array $options, NodeInterface $node = null)
    {
        if ($options['content'] instanceof Route && $options['content']->hasOption('currentUriPrefix')) {
            $currentUriPrefix = $options['content']->getOption('currentUriPrefix');
            $currentUriPrefix = str_replace('{_locale}', $request->getLocale(), $currentUriPrefix);
            if (0 === strncmp($request->getPathinfo(), $currentUriPrefix, strlen($currentUriPrefix))) {
                return self::VOTE_YES;
            }
        }

        return self::VOTE_ABSTAIN;
    }
}
