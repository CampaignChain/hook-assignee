<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CampaignChain\Hook\AssigneeBundle\Form\Type;

use CampaignChain\CoreBundle\Form\Type\HookType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class AssigneeType extends HookType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'label' => false,
                'class' => 'CampaignChainCoreBundle:User',
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.username', 'ASC');
                    },
                'choice_label' => 'nameAndUsername',
                'placeholder' => 'Select responsible person',
                'empty_data' => null,
                'attr' => array(
                    'show_image' => true,
                )
            ));
    }

    public function getBlockPrefix()
    {
        return 'campaignchain_hook_campaignchain_assignee';
    }
}