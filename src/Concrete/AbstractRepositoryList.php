<?php

namespace Concrete\Package\AttributeForms;

use Concrete\Core\Search\ItemList\ItemList as AbstractItemList;
use Concrete\Package\AttributeForms\Repository\Search\Pagination;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineSelectableAdapter;
use Concrete\Package\AttributeForms\MeschApp;

use Database,
    Exception;

/**
 * @method Pagination getPagination()
 */
abstract class AbstractRepositoryList extends AbstractItemList
{
    /** @var Doctrine\Common\Collections\Criteria */
    protected $criteria;
    
    private $orderings;

    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    public function __construct()
    {
        $this->criteria  = Criteria::create();
        $this->orderings = array();
        $this->em  = MeschApp::em();
    }

    /**
     * @return Criteria
     */
    public final function getQueryObject()
    {
        return $this->criteria;
    }

    protected abstract function getEntityClassName();
    
    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getEntityClassName());
    }

    /**
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    protected function createPaginationObject()
    {
        $adapter    = new DoctrineSelectableAdapter($this->getRepository(), $this->getQueryObject());
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    public function executeGetResults()
    {
        if (!empty($this->orderings)) {
            $this->criteria->orderBy($this->orderings);
        }
        return $this->getRepository()->matching($this->criteria);
    }

    public final function getResult($mixed)
    {
        throw new Exception(t('Unused method'));
    }

    public final function getResults()
    {
        $this->debugStart();

        $executeResults = $this->executeGetResults()->toArray();

        $this->debugStop();

        return $executeResults;
    }

    public function getTotalResults()
    {
        return $this->executeGetResults()->count();
    }

    public function getOne()
    {
        return $this->executeGetResults()->first();
    }

    public function debugStart()
    {
        if ($this->isDebugged()) {
            Database::get()->getConfiguration()->setSQLLogger(new EchoSQLLogger());
        }
    }

    public function debugStop()
    {
        if ($this->isDebugged()) {
            Database::get()->getConfiguration()->setSQLLogger(null);
        }
    }

    protected function executeSortBy($column, $direction = 'asc')
    {
        if (in_array(strtolower($direction), array('asc', 'desc'))) {
            $this->orderings[$column] = $direction;
        } else {
            throw new Exception(t('Invalid SQL in order by'));
        }
    }

    protected function executeSanitizedSortBy($column, $direction = 'asc')
    {
        if (preg_match('/[^0-9a-zA-Z\$\.\_\x{0080}-\x{ffff}]+/u', $column) === 0) {
            $this->executeSortBy($column, $direction);
        } else {
            throw new Exception(t('Invalid SQL in order by'));
        }
    }

    public function getPage()
    {
        if ($this->itemsPerPage > 0) {
            $pagination = $this->getPagination();
            return $pagination->getCurrentPageResults();
        } else {
            return $this->getResults();
        }
    }


    /**
     * Specifies an ordering for the query results.
     *
     * @param string $column  The ordering column.
     * @param string $direction The ordering direction.
     */
    public function addSortBy($column, $direction = 'asc')
    {
        $this->executeSortBy($column, $direction);
    }

    public function filter($field, $value, $comparison = '=')
    {
        if ($field == false) {
            $this->criteria->andWhere($value); // ugh
        } else {
            $this->criteria->andWhere(implode(' ',
                    array(
                $field, $comparison, $value
            )));
        }
    }
}