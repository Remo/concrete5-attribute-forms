<?php
namespace Concrete\Package\AttributeForms\Src\Repository\Search;

use \Concrete\Package\AttributeForms\Src\AbstractRepositoryList;
use \Pagerfanta\Adapter\AdapterInterface;
use \Pagerfanta\Pagerfanta;
use Core;

class Pagination extends \Concrete\Core\Search\Pagination\Pagination
{
    /** @var \Concrete\Package\MeschSupportVideo\Src\AbstractRepositoryList  */
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
