# Upgrade from 1.2 to 2.0

## Factory

* The `ContentAwareFactory` and with it the `cmf_menu.factory` service are removed. The `knp_menu.factory` service is now extended instead:

  **Before**
  ```yaml
  services:
      menu_builder:
          class: AppBundle\Menu\MenuBuilder
          arguments: ["@cmf_menu.factory"]
  ```

  **After**
  ```yaml
  services:
     menu_builder:
         class: AppBundle\Menu\MenuBuilder
         arguments: ["@knp_menu.factory"]
  ```

* The `loadFromNode` and `loadFromArrays` are removed from the factory, use the NodeLoader or ArrayLoader instead:

  **Before**
  ```php
  $menuFactory->loadFromNode(...);
  ```

  **After**
  ```php
  // the node loader is available as the cmf_menu.loader.node service
  $nodeLoader->load(...);
  ```

## Voters

* The `cmf_menu.voter` tag has been removed in favor of `knp_menu.voter`:

  **Before**
  ```yaml
  services:
      menu_voter:
          class: AppBundle\Menu\Voter\CurrentUrlVoter
          tags:
              - { name: cmf_menu.voter }
  ```

  **After**
  ```yaml
  services:
      menu_voter:
          class: AppBundle\Menu\Voter\CurrentUrlVoter
          tags:
              - { name: knp_menu.voter }
  ```

* Voters needing the master requests can now set the `request` option of the `knp_menu.voter` tag:

  **Before**
  ```yaml
  services:
      request_menu_voter:
          class: AppBundle\Menu\Voter\RequestVoter
          calls:
              - [setRequest, ["@?request="]]
          tags:
              - { name: cmf_menu.voter }
  ```

  **After**
  ```yaml
  services:
      request_menu_voter:
          class: AppBundle\Menu\Voter\RequestVoter
          tags:
              - { name: knp_menu.voter, request: true }
  ```

* The `Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface` is removed in favor of `Knp\Menu\Matcher\Voter\VoterInterface`:

  **Before**
  ```php
  // ...
  use Symfony\Cmf\Bundle\MenuBundle\Voter\VoterInterface;

  class CustomVoter implements VoterInterface
  {
      public function matchItem(ItemInterface $item = null)
      {
          // ...
      }
  }
  ```

  **After**
  ```php
  // ...
  use Knp\Menu\Matcher\Voter\VoterInterface;

  class CustomVoter implements VoterInterface
  {
      public function matchItem(ItemInterface $item = null)
      {
          // ...
      }
  }
  ```

## CreateMenuItemFromNodeEvent

* The event has no longer access to the menu factory:

  **Before**
  ```php
  class CreateMenuItemFromNodeListener
  {
      public function onEvent(CreateMenuItemFromNodeEvent $event)
      {
          $event->getFactory()->createItem(...);
      }
  }
  ```

  **After**
  ```php
  use Knp\Menu\FactoryInterface;

  class CreateMenuItemFromNodeListener
  {
      private $factory;

      // factory is available as the knp_menu.factory service
      public function __construct(FactoryInterface $factory)
      {
          $this->factory = $factory;
      }

      public function onEvent(CreateMenuItemFromNodeEvent $event)
      {
          $this->factory->createItem(...);
      }
  }
  ```
