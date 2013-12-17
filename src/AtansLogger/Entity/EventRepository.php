<?php
namespace AtansLogger\Entity;

use District\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class EventRepository extends EntityRepository
{
    /**
     * Get previous event
     *
     * @param  string $target
     * @param  int $objectId
     * @param  int $id
     * @return Event
     */
    public function getPreviousEvent($target, $objectId, $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $event = $qb->select('e')
                  ->from($this->getClassName(), 'e')
                  ->where($qb->expr()->eq('e.target', ':target'))
                  ->setParameter('target', $target)
                  ->andWhere($qb->expr()->eq('e.objectId', ':objectId'))
                  ->setParameter('objectId', $objectId)
                  ->andWhere($qb->expr()->lt('e.id', ':id'))
                  ->setParameter('id', $id)
                  ->orderBy('e.id', 'DESC')
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();

        return $event;
    }

    /**
     * Paginator
     *
     * @param array $data
     * @return Paginator
     * @throws \District\Exception\InvalidArgumentException
     */
    public function pagination(array $data)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('e')
            ->from($this->getEntityName(), 'e');

        if (isset($data['target']) && strlen($data['target']) > 0) {
            $qb->andWhere($qb->expr()->eq('e.target', ':target'))
               ->setParameter('target', $data['target']);
        }

        if (isset($data['name']) && strlen($data['name']) > 0) {
            $qb->andWhere($qb->expr()->eq('e.name', ':name'))
               ->setParameter('name', $data['name']);
        }

        if (isset($data['objectId']) && $data['objectId'] > 0) {
            $qb->andWhere($qb->expr()->eq('e.objectId', ':objectId'))
               ->setParameter('objectId', (int) $data['objectId']);
        }

        if (isset($data['createdBy']) && $data['createdBy'] > 0) {
            $qb->andWhere($qb->expr()->eq('e.createdBy', ':createdBy'))
               ->setParameter('createdBy', (int) $data['createdBy']);
        }

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

    /**
     * Get creators
     *
     * @return array
     */
    public function findCreators()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $result = $qb->select('IDENTITY(e.createdBy) AS createdBy, e.username')
                     ->from($this->getEntityName(), 'e')
                     ->where('e.createdBy > 0')
                     ->groupBy('e.createdBy')
                     ->getQuery()
                     ->getArrayResult();

        $creators = array();
        foreach ($result as $creator) {
            $creators[$creator['createdBy']] = sprintf('%s (#%d)', $creator['username'], $creator['createdBy']);
        }

        return $creators;
    }
}
