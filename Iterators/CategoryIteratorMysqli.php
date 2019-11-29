<?php

namespace Yml\Iterators;

use Yml\Models\Category;
use Yml\Models\ICategoryIterator;
use mysqli;
use mysqli_result;

class CategoryIteratorMysqli implements ICategoryIterator
{
    /** @var mysqli */
    private $pdo;
    /** @var  mysqli_result */
    private $mysqliResult;
    
    private $current;
    private $idx;
    
    private $totalFetchTime = 0;
    
    public function __construct(mysqli $mysqli)
    {
        $this->pdo = $mysqli;
    }

    public function current(): ?Category
    {
        $data = $this->current;

        if ($data === null) {

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
        $this->pdo->real_query('SELECT id, name, parent_id FROM category');
        /**
         * use_result - returns cursor, so it will not load memory at all (opposite store_result - load all result in memory)
         * NB! if MyIsam is uses use_result will block table(or block of rows) for update (use InnoDB instead)
         * 
         * Using Mysql PDO will load all result in memory too. So do not use it
         */
        $this->mysqliResult = $this->pdo->use_result();
        $time = number_format(microtime(true) - $time, 2);
        
        print("Query time: {$time}\n");
        
        $this->totalFetchTime = 0;
        $this->current = $this->fetch();
        $this->idx = 0;
    }
    
    private function fetch()
    {
        $time = microtime(true);
        $data = $this->mysqliResult->fetch_assoc();
        $this->pdo->next_result();
        $this->totalFetchTime += microtime(true) - $time;
        
        if (is_null($data)) {

            print "\nTotal fetch time: {$this->totalFetchTime}\n";
        }
        
        return $data;
    }
}
