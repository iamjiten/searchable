<?php

namespace TheCodingHabit\Searchable\Traits;

use Illuminate\Support\Facades\Schema;

trait SearchableTrait
{
    public function scopeSearch($query, $search_term)
    {
        $model_column = Schema::getColumnListing($this->getTable());

        $searchables = [];

        if (property_exists($this, 'searchable_columns') && count($this->searchable_columns)) {
            $searchables = $this->searchable_columns;
        } else if (property_exists($this, 'fillable') && count($this->getFillable())) {
            $searchables = $this->getFillable();
        } else {
            $searchables = $model_column;
        }
        $unsetFields = ['id', 'created_at', 'updated_at'];
        $searchables = array_diff($searchables, $unsetFields);

        $query->where($searchables[0], 'LIKE', "%{$search_term}%");
        $searchables = array_slice($searchables, 1);

        foreach ($searchables as $searchable) {
            $query->orWhere($searchable, 'LIKE', "%{$search_term}%");
        }

        return $query;
    }
}
