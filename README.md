# Symfony Cmf Menu Bundle

This is part of the Symfony Cmf: <http://cmf.symfony.com/>


## Documentation
<http://symfony.com/doc/master/cmf/bundles/menu.html>


## Installation

The general installation documentation for the CMF can be found here:
<http://symfony.com/doc/master/cmf/tutorials/installing-configuring-cmf.html>

This chapter is only valuable if you want to use this bundle alone. 

The bundle is best included using Composer.

Edit your project composer file to add a new require for
`symfony-cmf/menu-bundle`.

Add this bundle (and its dependencies, if they are not already there) to your
application's kernel:

	// application/ApplicationKernel.php
	public function registerBundles()
	{
			return array(
			// ...
			new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Symfony\Cmf\Bundle\MenuBundle\SymfonyCmfMenuBundle(),
			// ...
		);
	}
