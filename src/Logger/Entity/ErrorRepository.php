<?php
namespace AtansLogger\Entity;

use District\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class ErrorRepository extends EntityRepository
{
    public function pagination($data)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('l')
            ->from($this->getEntityName(), 'l');

        if (isset($data['priority']) && strlen($data['priority'])) {
            $qb->andWhere($qb->expr()->eq('l.priority', ':priority'))
               ->setParameter('priority', $data['priority']);
        }

        if (isset($data['query']) && strlen($queryString = trim($data['query']))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('l.message', ':query'),
                $qb->expr()->like('l.file', ':query'),
                $qb->expr()->like('l.trace', ':query')
            ));
            $qb->setParameter('query', "%$queryString%");
        }

        $order = 'DESC';
        if (isset($data['order']) && in_array(strtoupper($data['order']), array('ASC', 'DESC'))) {
            $order = $data['order'];
        }
        $qb->addOrderBy('l.id', $order);

        if (!isset($data['page']) || !isset($data['size'])) {
            throw new Exception\InvalidArgumentException(
                "'page' and 'size' are must be defined"
            );
        }

        $paginator = new Paginator(new DoctrinePaginator(
            new ORMPaginator($qb)
        ));

        $paginator->setCurrentPageNumber($data['page'])
                  ->setItemCountPerPage($data['size']);

        return $paginator;
    }
}
