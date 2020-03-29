<?php

namespace Yml\Iterators;

use Phalcon\Mvc\Model\ResultsetInterface;
use Yml\Models\IOfferIterator;
use Yml\Models\Offer;

class OfferIteratorPhalconModelResultSet implements IOfferIterator
{
    /** @var ResultsetInterface */
    private $resultSet;
    private $idx;

    private $totalFetchTime;


    public function current(): ?Offer
    {
        $data = $this->resultSet->current();

        if ($data === false) {

            return null;
        }

        $offer = (new Offer())
            ->setId($data['id'])
            ->setAvailable($data['available'])
            ->setUrl($data['url'])
            ->setPrice($data['price'])
            ->setCategoryId($data['category_id']);

        return $offer;
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
        $this->resultSet = \PhalconModels\Offer::query()
            ->createBuilder()
            ->from(['o' => \PhalconModels\Offer::class])
            ->columns(['o.id', 'o.available', 'o.price', 'o.url', 'o.category_id'])
            ->getQuery()
            ->execute();

        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = $this->resultSet->count();
        print("Total fetched offers: {$totalFetchedCategories}\n");


        $this->resultSet->rewind();
        $this->idx = 0;
        $this->totalFetchTime = 0;
    }
}
