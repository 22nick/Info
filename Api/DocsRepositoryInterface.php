<?php
namespace Tunik\Info\Api;

use Tunik\Info\Api\Data\DocsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface DocsRepositoryInterface 
{
    public function save(DocsInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(DocsInterface $page);

    public function deleteById($id);
}
