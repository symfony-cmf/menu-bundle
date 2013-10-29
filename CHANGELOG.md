Changelog
=========

1.0.1
-----

* **2013-10-29**: Fix MenuNodeReferrersExtension to also work when adding an
  additional menu item if there already exists one. Allow to edit the menu item
  name even after creation as in all other admins - this prevented adding
  another menu item in the menu tab created by the admin extension. If you want
  the old behaviour back, extend the admin and change the definition of the
  `name` field, adding back what is removed in the pull request
  https://github.com/symfony-cmf/MenuBundle/pull/157

1.0.0
-----

First stable release.

1.0.0-RC4
---------

* **2013-09-02**: Removed __toString() method

1.0.0-RC2
---------

* **2013-08-04**: [PHPCR-ODM] properly map nullable properties as nullable

1.0.0-RC1
---------

* **2013-07-28**: [DependencyInjection] moved phpcr specific configuration
  under `persistence.phpcr`.
* **2013-07-27**: Removed `MenuCommonAdmin::getBlocks` as not sure what uses it.
* **2013-07-27**: Refactored all feature structure. Minimal persistable menu is
 now `Menu[Node]Base`, whereas everything else including **translatable** is
 in `Menu[Node]`. Consequently we have removed **Multilang** admin and we
 provide **just one admin class** per document.
 Note that additional fields are now mapped into storage, most important the
 `display` and `displayChildren` fields, which needs to be set to `true` for
 existing PHPCR documents to keep menu nodes showing up.

 To migrate, use the following script:

     $ php app/console doctrine:phpcr:nodes:update \
        --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = \"Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\MultilangMenuNode\"" \
        --set-prop=phpcr:class="Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\MenuNode" \
        --set-prop=display="true"
        --set-prop=displayChildren="true"

* **2013-07-19**: Removed choice of weak/strong content reference. Standard is now weak. Migration
  as follows (you may need to adjust the [phpcr:class] clause to match your implementation):

       $ php app/console doctrine:phpcr:nodes:update \
           --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = 'Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\MenuNode' OR [phpcr:class] = 'Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\MultilangMenuNode'" \
           --apply-closure="if (\!\$node->hasProperty('weakContent') && \!\$node->hasProperty('strongContent')) { return; }; \$node->setProperty('menuContent', \$node->getProperty('weak')->getValue() == 1 ? \$node->getProperty('weakContent')->getString() : \$node->getProperty('hardContent')->getString(), PropertyType::WEAKREFERENCE);"

* **2013-07-16**: [Model] Adopted persistance standard model, see: http://symfony.com/doc/master/cmf/contributing/bundles.html#Persistence.

  To migrate adapt the following script. Run it once for each document class, replacing <documentClass> with `MenuNode`, `Menu`, `MultilangMenu` and `MultilangMenuNode` respectively:

    $ php app/console doctrine:phpcr:nodes:update \
        --query="SELECT * FROM [nt:unstructured] WHERE [phpcr:class] = \"Symfony\\Cmf\\Bundle\\MenuBundle\\Document\\<documentClass>\"" \
        --set-prop=phpcr:class="Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\<documentClass>"

1.0.0-beta2
-----------

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
* **2013-06-25**: Added "linkType" property. Menu nodes can now optionally
                  define which type of link should be used - e.g. route,
                  content or uri.
* **2013-06-21**: Added the missing options from knp-menu to the MenuNode
* **2013-06-12**: [Document] Renamed "strongContent" to "hardContent" to better
                  reflect the PHPCR terminology
* **2013-06-07**: Added publish work flow implementation
