<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
                'property' => 'nameAndUsername',
                'empty_value' => 'Select responsible person',
                'empty_data' => null,
            ));
    }

    public function getName()
    {
        return 'campaignchain_hook_campaignchain_assignee';
    }
}