# Upgrade from 2.0 to 2.1

## Model

* Removed `setParent()`/`getParent()` from the model `MenuNode`. Use
  `setParentObject()`/`getParentObject()` instead.

  **Before**
  ```php
  use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
  // ...

  $node = new MenuNode('some name');
  $node->setParent($menuRoot);
  ```

  **After**
  ```php
  use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
  // ...

  $node = new MenuNode('some name');
  $node->setParentObject($menuRoot);
  ```
