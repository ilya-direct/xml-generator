<?php

namespace Yml\Iterators;

use Phalcon\Mvc\Model\ResultsetInterface;
use Yml\Models\Category;
use Yml\Models\ICategoryIterator;

class CategoryIteratorPhalconModelResultSet implements ICategoryIterator
{
    /** @var ResultsetInterface */
    private $resultSet;
    private $idx;

    private $totalFetchTime;


    public function current(): ?Category
    {
        $data = $this->resultSet->current();

        if ($data === false) {

            return null;
        }

        $category = (new Category())
            ->setId($data['id'])
            ->setName($data['name'])
            ->setParentId($data['parent_id']);

        return $category;
    }

    public function next()
    {
        $this->idx++;
        $time = microtime(true);
        $data = $this->resultSet->next();
        $this->totalFetchTime += microtime(true) - $time;

        return $data;
    }

    public function key()
    {
        return $this->idx;
    }

    public function valid()
    {
        return $this->resultSet->valid();
    }

    public function rewind()
    {
        $time = microtime(true);
        $this->resultSet = \PhalconModels\Category::query()
            ->createBuilder()
            ->from(['c' => \PhalconModels\Category::class])
            ->columns(['c.id', 'c.name', 'c.parent_id'])
            ->getQuery()
            ->execute();

        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = $this->resultSet->count();
        print("Total fetched categories: {$totalFetchedCategories}\n");


        $this->resultSet->rewind();
        $this->idx = 0;
        $this->totalFetchTime = 0;
    }
}
