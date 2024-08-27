<?php

namespace App\Collection;

use App\Model\ArticleSource;
use InvalidArgumentException;

class ArticleSourceCollection implements CollectionInterface
{
    use CollectionTrait;
    public function offsetGet(mixed $offset): ArticleSource
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if(!$value instanceof ArticleSource){
            throw new InvalidArgumentException('$value must be instance of ArticleSource');
        }
        if($offset === null){
            $offset = $this->position;
        }
        $this->items[$offset] = $value;
    }
}