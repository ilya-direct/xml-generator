<?php

namespace Yml\Models;

class Offer
{
    private $id = 0;
    private $available = 'false';
    private $url = '';
    private $price = 0;
    private $categoryId = 0;
    
    public function setId(int $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function id(): int
    {
        return $this->id;
    }
    
    public function setAvailable(bool $available): self
    {
        $this->available = $available
            ? 'true'
            : 'false';
        
        return  $this;
    }

    public function available(): string
    {
        return $this->available;
    }
    
    public function setUrl(string $url): self
    {
        $this->url = $url;
        
        return $this;
    }

    public function url(): string
    {
        return $this->url;
    }
    
    public function setPrice(int $price): self
    {
        $this->price = $price;
        
        return $this;
    }
    
    public function price(): int
    {
        return $this->price;
    }
    
    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;
        
        return $this;
    }
    

    public function categoryId(): int
    {
        return $this->categoryId;
    }
}
