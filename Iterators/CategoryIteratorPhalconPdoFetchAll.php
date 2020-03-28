<?php

namespace Yml\Iterators;

use Phalcon\Db;
use Phalcon\Db\AdapterInterface;
use Yml\Models\Category;
use Yml\Models\ICategoryIterator;
use PDO;

class CategoryIteratorPhalconPdoFetchAll implements ICategoryIterator
{
    /** @var AdapterInterface */
    private $pdo;
    
    private $categories;
    
    public function __construct(AdapterInterface $pdo)
    {
        $this->pdo = $pdo;
    }

    public function current(): ?Category
    {
        $data = current($this->categories);

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
        $time = microtime(true);
        $this->categories = $this->pdo->fetchAll('SELECT id, name, parent_id FROM category ORDER BY id', Db::FETCH_ASSOC);
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = count($this->categories);
        print("Total fetched categories: {$totalFetchedCategories}\n");
    }
}
