<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\FactoryInterface;
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
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * Whether to return null (if value is false) or a MenuItem
     * without any URL (if value is true) if no URL can be found
     * for a MenuNode.
     *
     * @var bool
     */
    private $allowEmptyItems;

    public function __construct(FactoryInterface $innerFactory, LoggerInterface $logger = null, $allowEmptyItems = false)
    {
        $this->innerFactory = $innerFactory;
        $this->logger = $logger;
        $this->allowEmptyItems = $allowEmptyItems;
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($name, array $options = array())
    {
        try {
            return $this->innerFactory->createItem($name, $options);
        } catch (RouteNotFoundException $e) {
            if (null !== $this->logger) {
                $this->logger->error(
                    sprintf('An exception was thrown while creating a menu item called "%s"', $name),
                    array('exception' => $e)
                );
            }

            if (!$this->allowEmptyItems) {
                return;
            }

            // remove route and content options
            unset($options['route']);
            unset($options['content']);

            return $this->innerFactory->createItem($name, $options);
        }
    }
}
