<?php

namespace Yml\Models;

use Iterator;

interface IOfferIterator extends Iterator
{
    public function current(): ?Offer;
}
