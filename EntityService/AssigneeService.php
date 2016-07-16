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

namespace CampaignChain\Hook\AssigneeBundle\EntityService;

use CampaignChain\CoreBundle\Entity\AssignableInterface;
use CampaignChain\CoreBundle\EntityService\HookServiceDefaultInterface;
use CampaignChain\Hook\AssigneeBundle\Model\Assignee;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Inflector\Inflector;

class AssigneeService implements HookServiceDefaultInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getHook($entity){
        $hook = new Assignee();
        if($entity && $entity->getId() && $entity instanceof AssignableInterface){
            $hook->setUser($entity->getAssignee());
        }
        return $hook;
    }

    public function processHook($entity, $hook){
        if (!$entity instanceof AssignableInterface) {
            return $entity;
        }

        $entity->setAssignee($hook->getUser());

        $this->em->persist($entity);
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