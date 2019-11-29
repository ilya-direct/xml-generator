<?php

namespace Yml\Iterators;

use Yml\Models\Category;
use Yml\Models\ICategoryIterator;
use PDO;
use PDOStatement;

class CategoryIteratorPdoFetch implements ICategoryIterator
{
    /** @var PDO */
    private $pdo;
    /** @var PDOStatement */
    private $stmt;
    
    private $current;
    private $idx;
    
    private $totalFetchTime;
    
    public function __construct(PDO $pdo)
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
        $stmt = $this->pdo->query('SELECT id, name, parent_id FROM category');
        
        if (((int)$this->pdo->errorCode()) !== 0) {
            
            throw new \Exception($this->pdo->errorInfo()[3]);
        }
        
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = $stmt->rowCount();
        print("Total fetched categories: {$totalFetchedCategories}\n");
        
        $this->stmt = $stmt;
        $this->current = $this->fetch();
        $this->idx = 0;
        $this->totalFetchTime = 0;
    }
    
    private function fetch()
    {
        $time = microtime(true);
        $data = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $this->totalFetchTime += microtime(true) - $time;
        
        return $data;
    }
}
