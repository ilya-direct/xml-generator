<?php

namespace Yml\Iterators;

use Yml\Models\IOfferIterator;
use Yml\Models\Offer;

class OfferIteratorArray implements IOfferIterator
{
    private $offers;
    
    public function __construct(array $offers)
    {
        $this->offers = $offers;
    }
    
    public function current(): ?Offer
    {
        $offer = current($this->offers);
        
        if ($offer === false) {
            
            return null;
        }
        
        $offer = (new Offer())
            ->setId($offer['id'])
            ->setAvailable($offer['available'])
            ->setUrl($offer['url'])
            ->setPrice($offer['price'])
            ->setCategoryId($offer['category_id']);
        
        return $offer;
    }

    public function next()
    {
        return next($this->offers);
    }

    public function key()
    {
        return key($this->offers);
    }

    public function valid()
    {
        return current($this->offers) != null;
    }

    public function rewind()
    {
        return reset($this->offers);
    }
    
    public function count(): int
    {
        return count($this->offers);
    }
}
