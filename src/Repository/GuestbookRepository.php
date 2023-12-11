<?php

namespace App\Repository;

use App\Entity\Guestbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GuestbookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guestbook::class);
    }

    public function getLastItems(bool $onlyApproved)
    {
        $qb = $this->createQueryBuilder('guestbook')
            ->orderBy('guestbook.id', 'DESC');

        if ($onlyApproved) {
            $qb->andWhere($qb->expr()->eq('guestbook.isApproved', ':isApproved'))
                ->setParameter(':isApproved', true);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function save(Guestbook $guestbook, bool $flush = false)
    {
        $this->getEntityManager()->persist($guestbook);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Guestbook $guestbook, bool $flush = false)
    {
        $this->getEntityManager()->remove($guestbook);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
