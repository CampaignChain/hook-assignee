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
use CampaignChain\Hook\AssigneeBundle\Model\Assignee;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AssigneeType extends HookType
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);
        ;
    }

    public function preSetData(FormEvent $event)
    {
        /**
         * @var $data Assignee
         */
        $data = $event->getData();

        if (!$data->getUser()) {
            return;
        }

        $this->entityManager->persist($data->getUser());
    }

    public function getName()
    {
        return 'campaignchain_hook_campaignchain_assignee';
    }
}