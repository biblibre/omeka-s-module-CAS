<?php

namespace CAS\Service\Form;

use CAS\Form\ConfigForm;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ConfigFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, ?array $options = null)
    {
        $form = new ConfigForm(null, $options ?? []);

        $form->setModuleManager($services->get('Omeka\ModuleManager'));

        return $form;
    }
}
