<?php
namespace Concrete\Package\AttributeForms\Repository\Search;

use Concrete\Package\AttributeForms\AbstractRepositoryList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

class Pagination extends \Concrete\Core\Search\Pagination\Pagination
{
    /** @var \Concrete\Package\AttributeForms\AbstractRepositoryList  */
    protected $list;

    public function __construct(AbstractRepositoryList $itemList, AdapterInterface $adapter)
    {
        $this->list = $itemList;

        return Pagerfanta::__construct($adapter);
    }

    public function getItemListObject()
    {
        return $this->list;
    }

    public function getCurrentPageResults()
    {
        $this->list->debugStart();

        $results = Pagerfanta::getCurrentPageResults()->toArray();

        $this->list->debugStop();

        return $results;
    }
}
