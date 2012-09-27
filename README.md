# Symfony Cmf Menu Bundle

This is part of the Symfony Cmf: <https://github.com/symfony-cmf/symfony-cmf>

## Configuration

There are some items you can configure:

- menu_basepath:
    default: /cms/menu - the path for the menus in the content repository
- document_manager_name:
    default: default - the name of the document manager
- menu_document_class:
    default: null - the name of the class of the menu documents
- content_url_generator:
    default: router
- content_key:
    default: null
- route_name:
    default: null
- use_sonata_admin:
    default: auto - set this to false if you have sonata admin in your project
        but do not want to use the provided admin service for menu items
- content_basepath:
    default: taken from the core bundle or /cms/content - used for the menu admin

## Links

- GitHub: <https://github.com/symfony-cmf/symfony-cmf>
- Sandbox: <https://github.com/symfony-cmf/cmf-sandbox>
- Web: <http://cmf.symfony.com/>
- Wiki: <http://github.com/symfony-cmf/symfony-cmf/wiki>
- Issue Tracker: <http://cmf.symfony-project.org/redmine/>
- IRC: irc://freenode/#symfony-cmf
- Users mailing list: <http://groups.google.com/group/symfony-cmf-users>
- Devs mailing list: <http://groups.google.com/group/symfony-cmf-devs>

## Documentation

This bundle extends [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) in order to work with PHPCR ODM. It can go through a [PHPCR](http://phpcr.github.com/) repository and build the corresponding menu. 

The [CMF website](http://cmf.symfony.com) is a concrete example of code using this bundle. It uses the CMF MenuBundle with a custom menu provider, on top of a SQLite PHPCR repository. 

### Installation

This bundle is best included using Composer.

Edit your project composer.json file to add a new require for symfony-cmf/menu-bundle.

	"require": {
        "php": ">=5.3.3",
        "symfony/symfony": "2.1.*",
		"symfony-cmf/symfony-cmf": "1.0.*",
		"symfony-cmf/simple-cms-bundle": "1.0.*",
		"symfony-cmf/menu-bundle": "1.0.*"

		//optional dependencies
        "sonata-project/doctrine-phpcr-admin-bundle": "1.0.*",

        },

Add this bundle (and its dependencies, if they are not already there) to your application's kernel:

	// application/ApplicationKernel.php
	public function registerBundles()
	{
			return array(
			// ...
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Symfony\Cmf\Bundle\SimpleCmsBundle\SymfonyCmfSimpleCmsBundle(),
			new Symfony\Cmf\Bundle\RoutingExtraBundle\SymfonyCmfRoutingExtraBundle(),
			new Symfony\Cmf\Bundle\MenuBundle\SymfonyCmfMenuBundle(),

			// optional dependencies:
			new Sonata\AdminBundle\SonataAdminBundle(),
			new Sonata\DoctrinePHPCRAdminBundle\SonataDoctrinePHPCRAdminBundle(),

			// ...
		);
	}

### Configuration

Add a mapping to `config.yml`, for the knp_menu and for the CMF menu.

	knp_menu:
		twig: true

	symfony_cmf_menu:
		use_sonata_admin: false
		menu_basepath: /cms

### Usage

Adjust your template to load the menu.

	{{ knp_menu_render('simple') }}


If your PHPCR repository stores the nodes under `/cms/simple`, use the `simple` alias as argument of `knp_menu_render`.
