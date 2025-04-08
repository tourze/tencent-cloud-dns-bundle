<?php

namespace TencentCloudDnsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudDnsBundle\Entity\DnsDomain;

/**
 * @method DnsDomain|null find($id, $lockMode = null, $lockVersion = null)
 * @method DnsDomain|null findOneBy(array $criteria, array $orderBy = null)
 * @method DnsDomain[] findAll()
 * @method DnsDomain[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DnsDomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DnsDomain::class);
    }
}
