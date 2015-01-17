<?php

namespace Fuz\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Fuz\AppBundle\Entity\CaptchaSessionIp;

class CaptchaSessionIpRepository extends EntityRepository
{

    public function deleteExpired(\DateTime $expiry)
    {
        $query = $this->_em->createQuery("
            DELETE Fuz\AppBundle\Entity\CaptchaSessionIp csi
            WHERE csi.creationTm < :expiry_date
        ");

        $params = array (
                'expiry_date' => $expiry,
        );

        $query->execute($params);
    }

    public function record($ip, $sessionId)
    {
        $entity = $this->findOneBy(array (
                'ip' => $ip,
                'sessionId' => $sessionId,
        ));

        if (!$entity)
        {
            $new = new CaptchaSessionIp();
            $new->setIp($ip);
            $new->setSessionId($sessionId);
            $this->_em->persist($new);
            $this->_em->flush();
        }
    }

    public function count($ip)
    {
        $query = $this->_em->createQuery("
            SELECT COUNT(csi.ip)
            FROM Fuz\AppBundle\Entity\CaptchaSessionIp csi
            WHERE csi.ip = :ip
        ");

        $params = array (
                'ip' => $ip,
        );

        $count = $query
           ->setParameters($params)
           ->getSingleScalarResult()
        ;

        return $count;
    }

}
