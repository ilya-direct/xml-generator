<?php

namespace Yml\Iterators;

use Yml\Models\IOfferIterator;
use Yml\Models\Offer;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\ResultInterface;
use Phalcon\Db;

class OfferIteratorPhalconPdo implements IOfferIterator
{
    /** @var PDO */
    private $pdo;
    /** @var ResultInterface */
    private $result;

    private $current;
    private $idx;

    private $totalFetchTime;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function current(): ?Offer
    {
        $data = $this->current;

        if ($data === false) {

            return null;
        }

        // TODO: checks

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
        $result = $this->pdo->query('SELECT id, available, price, url, category_id FROM offer ORDER BY url');
        $result->setFetchMode(
            Db::FETCH_ASSOC
        );
        
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = $result->numRows();
        print("Total fetched offers: {$totalFetchedCategories}\n");


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
