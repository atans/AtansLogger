<?php
namespace AtansLogger\Entity;

use District\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class EventRepository extends EntityRepository
{
    public function pagination($data)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('e')
            ->from($this->getEntityName(), 'e');

        if (isset($data['query']) && strlen($queryString = trim($data['query']))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('e.message', ':query')
            ));
            $qb->setParameter('query', "%$queryString%");
        }

        $order = 'DESC';
        if (isset($data['order']) && in_array(strtoupper($data['order']), array('ASC', 'DESC'))) {
            $order = $data['order'];
        }
        $qb->addOrderBy('e.id', $order);

        if (!isset($data['page']) || !isset($data['count'])) {
            throw new Exception\InvalidArgumentException(
                "'page' and 'size' are must be defined"
            );
        }

        $paginator = new Paginator(new DoctrinePaginator(
            new ORMPaginator($qb)
        ));

        $paginator->setCurrentPageNumber($data['page'])
                  ->setItemCountPerPage($data['count']);

        return $paginator;
    }
}
