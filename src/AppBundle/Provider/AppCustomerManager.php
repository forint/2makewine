<?php

namespace AppBundle\Provider;

use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class AppCustomerManager extends BaseEntityManager implements CustomerManagerInterface
{
    public function save($entity, $andFlush = true)
    {
        $this->checkObject($entity);
        // $entity= $this->getObjectManager()->merge($entity);

        $this->getObjectManager()->persist($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('c');

        $fields = $this->getEntityManager()->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!in_array($field, $fields)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }
        if (0 == count($sort)) {
            $sort = ['lastname' => 'ASC'];
        }
        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('c.%s', $field), strtoupper($direction));
        }

        $parameters = [];

        if (isset($criteria['is_fake'])) {
            $query->andWhere('c.isFake = :isFake');
            $parameters['isFake'] = $criteria['is_fake'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
