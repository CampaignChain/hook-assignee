<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Hook\AssigneeBundle\EntityService;

use CampaignChain\CoreBundle\EntityService\HookServiceDefaultInterface;
use CampaignChain\Hook\AssigneeBundle\Entity\Assignee;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Inflector\Inflector;

class AssigneeService implements HookServiceDefaultInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

//    public function newObject($entityClass, $entityId, $formData){
//        $assignee = new Assignee();
//        $assignee->setEntityId($entityId);
//        $assignee->setEntityClass($entityClass);
//        $assignee->setUser($formData['user']);
//
//        return $assignee;
//    }

    public function getHook($entity){
        if(!$entity || $entity->getId() === null){
            $hook = new Assignee();
        } else {
            $hook = $this->em->getRepository('CampaignChainHookAssigneeBundle:Assignee')->findOneBy(array(
                'entityClass' => get_class($entity),
                'entityId' => $entity->getId(),
            ));
        }

        return $hook;
    }

    public function processHook($entity, $hook){
        if($hook->getId() === null){
            $hook->setEntityId($entity->getId());
            $hook->setEntityClass(get_class($entity));
        }

        $this->em->persist($hook);
        $this->em->flush();

        return $entity;
    }

    public function arrayToObject($hookData){
        if(is_array($hookData) && count($hookData)){
            $hook = new Assignee();
            foreach($hookData as $property => $value){
                // TODO: Research whether this is a security risk, e.g. if the property name has been injected via a REST post.
                $method = (string) 'set'.Inflector::classify($property);
                if($method == 'setUser' && !is_object($value)){
                    $value = $this->em->getRepository('CampaignChainCoreBundle:User')->find($value);
                }
                $hook->$method($value);
            }
        }

        return $hook;
    }

    public function tplInline($entity){
        $hook = $this->getHook($entity);
        return $this->template->render(
            'CampaignChainHookAssigneeBundle::inline.html.twig',
            array('hook' => $hook)
        );
    }
}