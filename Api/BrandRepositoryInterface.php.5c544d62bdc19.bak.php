<?php
namespace Tunik\info\Api;

use Tunik\info\Api\Data\BrandInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface BrandRepositoryInterface 
{
    public function save(BrandInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(BrandInterface $page);

    public function deleteById($id);
}
