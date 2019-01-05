<?php

namespace App\Core\Searcher\Eloquent;

class EloquentTextQuerier {
    /** @var EloquentMapper */
    private $mapper;

    public function __construct(EloquentMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * @param string $query
     * @return void
     */
    public function applyQuery(string $query): void {
        if (strlen($query) === 0) {
            return;
        }

        // @todo
//        $this->builder->where(function (EloquentBuilder $builder) use ($query, $searchInFields) {
//            foreach ($searchInFields as $field) {
//                $column = $this->fieldToColumn($field);
//
//                $builder->orWhere($column, 'like', '%' . $query . '%');
//            }
//        });
    }
}
