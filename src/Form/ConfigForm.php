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

namespace CAS\Form;

use Laminas\Form\Form;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'Text',
            'name' => 'url',
            'options' => [
                'label' => 'URL', // @translate
                'info' => 'URL of the CAS server', // @translate
            ],
            'attributes' => [
                'id' => 'url',
                'required' => true,
            ],
        ]);

        $this->add([
            'type' => 'Omeka\Form\Element\RoleSelect',
            'name' => 'role',
            'options' => [
                'label' => 'Role', // @translate
                'info' => 'Created users will have this role', // @translate
            ],
            'attributes' => [
                'id' => 'role',
                'required' => true,
            ],
        ]);

        $this->add([
            'type' => 'Text',
            'name' => 'user_id_attribute',
            'options' => [
                'label' => 'Attribute used as identifier', // @translate
                'info' => 'If set, this attribute will be used as the unique user identifier to find the corresponding Omeka S user account', // @translate
            ],
            'attributes' => [
                'id' => 'user_id_attribute',
                'required' => false,
            ],
        ]);

        $this->add([
            'type' => 'Text',
            'name' => 'user_name_attribute',
            'options' => [
                'label' => 'Attribute used as user name', // @translate
                'info' => 'If set, this attribute will be used as the user name when creating a new Omeka S user account', // @translate
            ],
            'attributes' => [
                'id' => 'user_name_attribute',
                'required' => false,
            ],
        ]);

        $this->add([
            'type' => 'Text',
            'name' => 'user_email_attribute',
            'options' => [
                'label' => 'Attribute used as user email', // @translate
                'info' => 'If set, this attribute will be used as the user email when creating a new Omeka S user account', // @translate
            ],
            'attributes' => [
                'id' => 'user_email_attribute',
                'required' => false,
            ],
        ]);

        $this->add([
            'type' => 'Checkbox',
            'name' => 'show_login_link_in_user_bar',
            'options' => [
                'label' => 'Show CAS login link in user bar', // @translate
            ],
            'attributes' => [
                'id' => 'show_login_link_in_user_bar',
                'required' => false,
            ],
        ]);

        $this->add([
            'type' => 'Radio',
            'name' => 'redirect_mode',
            'options' => [
                'label' => 'Redirect mode', // @translate
                'value_options' => [
                    'query' => 'Query argument ("redirect_url")', // @translate
                    'session' => 'Session value', // @translate
                    'query_then_session' => 'Query argument or session value', // @translate
                    'session_then_query' => 'Session value or query argument', // @translate
                ],
            ],
            'attributes' => [
                'id' => 'redirect_mode',
                'value' => 'query',
                'required' => false,
            ],
        ]);

        $this->getInputFilter()
            ->add([
                'name' => 'redirect_mode',
                'required' => false,
            ])
        ;
    }
}
