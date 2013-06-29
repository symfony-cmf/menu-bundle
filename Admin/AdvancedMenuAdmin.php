<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AdvancedMenuAdmin extends MenuAdmin
{
    /**
     * Those two properties are needed to make it possible
     * to have 2 Admin classes for the same Document / Entity
     */
    protected $baseRouteName = 'admin_bundle_menu_multilangmenu_list';
    protected $baseRoutePattern = 'bundle/menu/multilangmenu';

    /**
     * @var array
     */
    protected $locales;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param array  $locales
     */
    public function __construct($code, $class, $baseControllerName, $locales)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->locales = $locales;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->add('locales', 'choice', array('template' => 'SonataDoctrinePHPCRAdminBundle:CRUD:locales.html.twig'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_menu')
                ->add('locale', 'choice', array(
                    'choices' => array_combine($this->locales, $this->locales),
                    'empty_value' => '',
                ))
            ->end()
        ;

        parent::configureFormFields($formMapper);
    }

    public function getNewInstance()
    {
        $new = parent::getNewInstance();

        if ($this->hasRequest()) {
            $currentLocale = $this->getRequest()->attributes->get('_locale');

            if (in_array($currentLocale, $this->locales)) {
                $meta = $this->getModelManager()->getMetadata(get_class($new));
                $meta->setFieldValue($new, $meta->localeMapping, $currentLocale);
            }
        }

        return $new;
    }
}
