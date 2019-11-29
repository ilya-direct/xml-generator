<?php

namespace Yml\Iterators;

use Yml\Models\Category;
use Yml\Models\ICategoryIterator;
use PDO;

class CategoryIteratorPdoFetchAll implements ICategoryIterator
{
    /** @var PDO */
    private $pdo;
    
    private $categories;

    private $totalFetchTime;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function current(): ?Category
    {
        $data = current($this->categories);

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
        return next($this->categories);
    }

    public function key()
    {
        return key($this->categories);
    }

    public function valid()
    {
        return $this->current() != null;
    }
    
    public function rewind()
    {
        $stmt = $this->pdo->prepare('SELECT "id", "name", "parent_id" FROM "category" ORDER BY id');
        $stmt->execute();

        $time = microtime(1);
        $this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->totalFetchTime = microtime(1) - $time;
        
        reset($this->categories);
    }
    
    public function totalFetchTime(): float 
    {
        return $this->totalFetchTime;
    }
    
    public function count(): int
    {
        return count($this->categories);
    }
}
