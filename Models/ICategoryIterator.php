<?php

namespace Yml\Models;

use Iterator;

interface ICategoryIterator extends Iterator
{
    public function current(): ?Category;
}
