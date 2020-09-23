<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LogicException;
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
    public function createItem(string $name, array $options = []): ItemInterface
    {
        try {
            return $this->innerFactory->createItem($name, $options);
        } catch (RouteNotFoundException $e) {
            if (null !== $this->logger) {
                $this->logger->error(
                    sprintf('An exception was thrown while creating a menu item called "%s"', $name),
                    ['exception' => $e]
                );
            }

            if (!$this->allowEmptyItems) {
                return $this->innerFactory->createItem('');
            }

            // remove route and content options
            unset($options['route'], $options['content']);

            return $this->innerFactory->createItem($name, $options);
        }
    }

    /**
     * Forward adding extensions to the wrapped factory.
     *
     * @param ExtensionInterface $extension
     * @param int                $priority
     *
     * @throws \Exception if the inner factory does not implement the addExtension method
     */
    public function addExtension(ExtensionInterface $extension, $priority = 0)
    {
        if (!method_exists($this->innerFactory, 'addExtension')) {
            throw new LogicException(sprintf(
                'Wrapped factory "%s" does not have the method "addExtension".',
                get_class($this->innerFactory)
            ));
        }
        $this->innerFactory->addExtension($extension, $priority);
    }
}
