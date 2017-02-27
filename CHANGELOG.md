Changelog
=========

2.1.0
-----

* **2017-02-17**: [BC BREAK] Removed unused setRequest from PhpcrMenuProvider and changed properties to private.

2.1.0-RC2
---------

* **2017-02-09**: [BC BREAK] Added child restrictions to the `Menu` and `MenuNode` documents.
  See the UPGRADE guide for detailed information.

2.1.0-RC1
---------

* **2016-11-27**: [BC BREAK] Removed all admin integration in favor of the CmfSonataAdminIntegrationBundle.
* **2016-06-18**: [BC BREAK] Removed all `*.class` parameters.
* **2016-05-25**: Use "auto" for publish workflow enabled flag. If auto and
  CmfCoreBundle is not instantiated, publish workflow integration is not enabled.

2.0.0
-----

Released.

2.0.0-RC1
---------

* **2014-11-03**: [BC BREAK] The CreateMenuItemFromNodeEvent class no longer has a getFactory method, inject the factory as a service instead
* **2014-11-01**: [BC BREAK] The voters now need to use the voter mechanism of KnpMenu.
* **2014-10-31**: [BC BREAK] The cmf_menu.factory service has been removed, knp_menu.factory should be used instead
* **2014-10-30**: [BC BREAK] PhpcrMenuProvider now requires the NodeLoader as first argument instead of a FactoryInterface

1.2.0
-----

Release 1.2.0

1.2.0-RC1
---------

* **2014-07-11**: Added MenuOptionsExtension that adds the editing feature of menu options in Sonata Admin
* **2014-06-06**: Updated to PSR-4 autoloading
* **2014-05-21**: [BC BREAK when extending BaseMenuNode] addChild and
  removeChild now accepts every Knp\Menu\NodeInterface instead of only
  MenuNode.

1.1.1
-----

* **2014-05-14**: [BC BREAK when extending Model] Removed parent from
  MenuNodeBase classes as they are not required by knp menu.

1.1.0
-----

Release 1.1.0

1.1.0-RC3
---------

* **2014-04-30**: Moved parent handling from MenuNodeBase to MenuNode to conform
  with the CMF rules of base models being the minimal model. HierarchyInterface
  on PHPCR MenuNode.

1.1.0-RC2
---------

* **2014-04-11**: drop Symfony 2.2 compatibility

1.1.0-RC1
---------

* **2014-04-04**: The menu factory now raises an event when a menu item is
  built from a menu node. The event can be used to change the behaviour or
  skip building the menu item altogether.

* **2014-03-24**: setParent() and getParent() are now deprecated.
  Use setParentObject() and getParentObject() instead.
  When using Sonata admin, you can enable the ChildExtension from the CoreBundle.

* **2014-01-10**: The PhpcrMenuProvider now attempts to prefetch the whole menu
  node tree to reduce the number of requests to the PHPCR storage. You can
  tweak the behaviour with the configuration setting
  `cmf_menu.persistence.phpcr.prefetch`.

* **2013-11-28**: Added referenceable mixin by default to PHPCR Menu and
  MenuNode classes. Migration as follows:

       $ php app/console doctrine:phpcr:nodes:update \
           --query="SELECT * FROM [nt:base] WHERE [phpcr:class] = 'Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\Menu' OR [phpcr:class] = 'Symfony\\Cmf\\Bundle\\MenuBundle\\Doctrine\\Phpcr\\MenuNode'" \
           --apply-closure="$node->addMixin('mix:referenceable');"

* **2013-11-25**: [PublishWorkflow] added a `MenuContentVoter`, this voter
  decides that a menu node is not published if the content it is pointing to is
  not published.

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
