# Upgrade from 2.0 to 2.1

### SonataAdmin Support

 * The Admin extensions where moved into `symfony-cmf/sonata-admin-integration-bundle`.
   With the move, the admin extension service names also changed. If you are using one of the menu extensions,
   you need to adjust your configuration.
   
   Before:
   
   ```yaml
        # app/config/config.yml
     
        sonata_admin:
            extensions:
                cmf_menu.admin_extension.menu_options:
                     implements:
                         - Symfony\Cmf\Bundle\MenuBundle\Model\MenuOptionsInterface
                cmf_menu.admin_extension.menu_node_referrers:
                     implements:
                         - Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface
   ```

    After:
       
   ```yaml
        # app/config/config.yml
                
        sonata_admin:
            extensions:
                 cmf_sonata_admin_integration.menu.extension.menu_options:
                     implements:
                         - Symfony\Cmf\Bundle\MenuBundle\Model\MenuOptionsInterface
                 cmf_sonata_admin_integration.menu.extension.menu_node_referrers:
                     implements:
                         - Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface
   ```
   Admin service names also changed. If you are using the admin, you need to adjust your configuration,
   i.e. in the sonata dashboard:
   
   Before:
   
   ```yaml
        # app/config/config.yml
        sonata_admin:
            dashboard:
               groups:
                   content:
                       label: URLs
                       icon: '<i class="fa fa-file-text-o"></i>'
                       items:
                           - cmf_menu.menu_admin
                           - cmf_menu.node_admin
   ```

    After:
       
   ```yaml
        # app/config/config.yml
        sonata_admin:
           dashboard:
               groups:
                   content:
                       label: Menu
                       icon: '<i class="fa fa-file-text-o"></i>'
                       items:
                           - cmf_sonata_admin_integration.menu.menu_admin
                           - cmf_sonata_admin_integration.menu.node_admin
   ```

# Doctrine PHPCR ODM

 * Only `MenuNode` documents are allowed as children of the `Menu` and
   `MenuNode` documents. This behaviour can be changed by overriding the
   `child-class` setting of the PHPCR ODM mapping.

 * `PhpcrMenuProvider` had a `setRequest` method that was completely unused.
   The method is removed, as well as the `$request` property. If you extend
   the provider, check if you happened to rely on this method or the property.
