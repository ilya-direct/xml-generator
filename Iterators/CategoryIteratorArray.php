<?php

namespace Yml\Iterators;

use Yml\Models\Category;
use Yml\Models\ICategoryIterator;

class CategoryIteratorArray implements ICategoryIterator
{
    /** @var array */
    private $categories;

    /**
     * ArrayCategoryIterator constructor.
     * @param array $categories
     */
    public function __construct(array $categories)
    {
        $this->categories = $categories;
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
        reset($this->categories);
    }

    public function count(): int
    {
        return count($this->categories);
    }
}
