Changelog
=========

* **2013-06-26**: Introduced "Menu" nodes to act as root menu nodes and updated
                  admin interface to reflect this. Migrate each of your root
                  menu nodes as follows:

                  ````
                  $ php app/console doctrine:phpcr:node:touch --set-prop=phpcr:class="Symfony\\Cmf\\Bundle\\MenuBUndle\\Document\\Menu" /path/to/root/menu/node
                  ````

                  If you use sonata admin:
                  Instead of `cmf_menu.admin` you can chose between the
                  `cmf_menu.menu_admin` that allows to edit the whole menu tree
                  on one page and `cmf_menu.node_admin` that allows to edit
                  individual menu nodes as before. The same for the multilang
                  admin.
                  Also don't forget to add the new Menu and MultilangMenu to the
                  mapping of the document_tree valid children.

* **2013-06-21**: Added the missing options from knp-menu to the MenuNode
* **2013-06-12**: [Document] Renamed "strongContent" to "hardContent" to better
                  reflect the PHPCR terminology
* **2013-06-07**: Added publish work flow implementation
