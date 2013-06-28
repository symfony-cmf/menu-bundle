Changelog
=========

* **2013-06-26**: Introduced "Menu" nodes to act as root menu nodes and updated
                  admin interface to reflect this. Migrate each of your root
                  menu nodes as follows:
                  
                  ````
                  $ php app/console doctrine:phpcr:node:touch --set-prop=phpcr:class="Symfony\\Cmf\\Bundle\\MenuBUndle\\Document\\Menu" /path/to/root/menu/node
                  ````

* **2013-06-21**: Added the missing options from knp-menu to the MenuNode
* **2013-06-12**: [Document] Renamed "strongContent" to "hardContent" to better
                  reflect the PHPCR terminology
* **2013-06-07**: Added publish work flow implementation
