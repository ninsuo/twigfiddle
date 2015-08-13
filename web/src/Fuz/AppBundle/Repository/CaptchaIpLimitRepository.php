<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Fuz\AppBundle\Entity\CaptchaIpLimit;

class CaptchaIpLimitRepository extends EntityRepository
{
    public function deleteExpired(\DateTime $expiry)
    {
        $query = $this->_em->createQuery("
            DELETE Fuz\AppBundle\Entity\CaptchaIpLimit cil
            WHERE cil.updateTm < :expiry
        ");

        $params = array(
                'expiry' => $expiry,
        );

        $query->execute($params);
    }

    public function record($ip, $limit)
    {
        $entity = $this->findOneByIp($ip);
        if (!$entity) {
            $new = new CaptchaIpLimit();
            $new->setIp($ip);
            $new->setLimit($limit);
            $this->_em->persist($new);
            $this->_em->flush($new);
        }
    }

    public function increaseLimit($ip, $toAdd)
    {
        $entity = $this->findOneByIp($ip);
        if ($entity) {
            $entity->setLimit($entity->getLimit() + $toAdd);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }
    }
}
