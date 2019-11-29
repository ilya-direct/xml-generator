<?php

namespace Yml\Iterators;

use Phalcon\Db;
use Yml\Models\Category;
use Yml\Models\ICategoryIterator;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\ResultInterface;

class CategoryIteratorPhalconPdo implements ICategoryIterator
{
    /** @var PDO */
    private $pdo;
    /** @var ResultInterface */
    private $result;
    
    private $current;
    private $idx;
    
    private $totalFetchTime;
    
    public function __construct(Pdo $pdo)
    {
        $this->pdo = $pdo;
    }

    public function current(): ?Category
    {
        $data = $this->current;

        if ($data === false) {

            return null;
        }

        // TODO: checks

        $category = (new Category())
            ->setId($data['id'])
            ->setName($data['name'])
            ->setParentId($data['parent_id']);

        return $category;
    }

    public function next()
    {
        $this->current = $this->fetch();
        $this->idx++;
        
        return $this->current();
    }

    public function key()
    {
        return $this->idx;
    }

    public function valid()
    {
        return $this->current() != null;
    }

    public function rewind()
    {
        $time = microtime(true);
        $result = $this->pdo->query('SELECT id, name, parent_id FROM category');
        $result->setFetchMode(
            Db::FETCH_ASSOC
        );
        
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = $result->numRows();
        print("Total fetched categories: {$totalFetchedCategories}\n");
        
        $this->result = $result;
        $this->current = $this->fetch();
        $this->idx = 0;
        $this->totalFetchTime = 0;
    }
    
    private function fetch()
    {
        $time = microtime(true);
        $data = $this->result->fetch();
        $this->totalFetchTime += microtime(true) - $time;
        
        return $data;
    }
}
