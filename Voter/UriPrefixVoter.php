<?php
namespace Symfony\Cmf\Bundle\MenuBundle\Voter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

use Knp\Menu\ItemInterface;

/**
 * This voter checks if the content option is a Symfony Route instance and if
 * so compares its currentUriPrefix with the request path.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class UriPrefixVoter implements VoterInterface
{
    private $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function matchItem(ItemInterface $item)
    {
        $options = $item->getAttributes();
        if ($options['content'] instanceof Route && $options['content']->hasOption('currentUriPrefix')) {
            $currentUriPrefix = $options['content']->getOption('currentUriPrefix');
            $currentUriPrefix = str_replace('{_locale}', $this->request->getLocale(), $currentUriPrefix);
            if (0 === strncmp($this->request->getPathinfo(), $currentUriPrefix, strlen($currentUriPrefix))) {
                return true;
            }
        }

        return null;
    }
}
