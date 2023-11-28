<?php

/*
 * Copyright 2020 BibLibre
 *
 * This file is part of CAS.
 *
 * CAS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CAS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CAS.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace CAS;

use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $services)
    {
        $connection = $services->get('Omeka\Connection');
        $connection->exec('CREATE TABLE cas_user (id VARCHAR(255) NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_8DA51140A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $connection->exec('ALTER TABLE cas_user ADD CONSTRAINT FK_8DA51140A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $connection = $services->get('Omeka\Connection');
        $connection->exec('DROP TABLE IF EXISTS cas_user');
    }

    public function attachListeners($sharedEventManager)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        if ($settings->get('cas_show_login_link_in_user_bar', false)) {
            $sharedEventManager->attach('*', 'view.layout', function ($event) {
                $view = $event->getTarget();
                if ($view->status()->isSiteRequest() && $view->identity() === null) {
                    $view->headLink()->appendStylesheet($view->assetUrl('css/user-bar.css', 'CAS'));
                    $currentUrl = $view->url(null, [], ['query' => $view->params()->fromQuery()], true);
                    $CAS = [
                        'login_url' => $view->casLoginUrl(['redirect_url' => $currentUrl]),
                    ];
                    $view->headScript()->appendScript(sprintf('const CAS = %s', json_encode($CAS)));
                    $view->headScript()->appendFile($view->assetUrl('js/user-bar.js', 'CAS'));
                }
            });
        }
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');

        $form = $formElementManager->get('CAS\Form\ConfigForm');
        $form->setData([
            'url' => $settings->get('cas_url'),
            'role' => $settings->get('cas_role'),
            'user_id_attribute' => $settings->get('cas_user_id_attribute'),
            'user_name_attribute' => $settings->get('cas_user_name_attribute'),
            'user_email_attribute' => $settings->get('cas_user_email_attribute'),
            'show_login_link_in_user_bar' => $settings->get('cas_show_login_link_in_user_bar'),
            'redirect_mode' => $settings->get('cas_redirect_mode'),
        ]);

        return $renderer->formCollection($form, false);
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');

        $form = $formElementManager->get('CAS\Form\ConfigForm');
        $form->setData($controller->params()->fromPost());
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }

        $formData = $form->getData();
        $settings->set('cas_url', $formData['url']);
        $settings->set('cas_role', $formData['role']);
        $settings->set('cas_user_id_attribute', $formData['user_id_attribute']);
        $settings->set('cas_user_name_attribute', $formData['user_name_attribute']);
        $settings->set('cas_user_email_attribute', $formData['user_email_attribute']);
        $settings->set('cas_show_login_link_in_user_bar', $formData['show_login_link_in_user_bar']);
        $settings->set('cas_redirect_mode', $formData['redirect_mode']);

        return true;
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $acl = $this->getServiceLocator()->get('Omeka\Acl');
        $acl->allow(
            null,
            'CAS\Controller\Login'
        );
    }
}
