<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * A decoration of the MenuFactory to not break
 * the page if a url could not be generated.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class QuietFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $innerFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Whether to return null or a MenuItem without any URL if no URL can be
     * found for a MenuNode.
     *
     * @var bool
     */
    private $allowEmptyItems;

    public function __construct(FactoryInterface $innerFactory, LoggerInterface $logger, $allowEmptyItems = false)
    {
        $this->innerFactory = $innerFactory;
        $this->logger = $logger;
        $this->allowEmptyItems = $allowEmptyItems;
    }

    /**
     * {@inheritDoc}
     */
    public function createItem($name, array $options = array())
    {
        try {
            return $this->innerFactory->createItem($name, $options);
        } catch (RouteNotFoundException $e) {
            $this->logger->error(sprintf('%s : %s', $name, $e->getMessage()));

            if (!$this->allowEmptyItems) {
                return null;
            }
        }
    }
}
