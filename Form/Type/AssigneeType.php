<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Hook\AssigneeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class AssigneeType extends AbstractType
{
    private $campaign;

    public function setCampaign($campaign){
        $this->campaign = $campaign;
    }

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
                'property' => 'username',
                'empty_value' => 'Select responsible person',
                'empty_data' => null,
            ));
    }

    public function getName()
    {
        return 'campaignchain_hook_campaignchain_assignee';
    }
}