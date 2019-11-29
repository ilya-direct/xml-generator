<?php

namespace Yml\Iterators;

use Yml\Models\IOfferIterator;
use PDO;
use PDOStatement;
use Yml\Models\Offer;

class OfferIteratorPdoFetch implements IOfferIterator
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
        $stmt = $this->pdo->query('SELECT id, available, price, url, category_id FROM offer ORDER BY url');

        if (((int)$this->pdo->errorCode()) !== 0) {

            throw new \Exception($this->pdo->errorInfo()[3]);
        }
        
        $time = number_format(microtime(true) - $time, 2);
        print("Query time: {$time}\n");
        $totalFetchedCategories = $stmt->rowCount();
        print("Total fetched offers: {$totalFetchedCategories}\n");


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
