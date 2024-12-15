<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SellerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // $builder->has('products'); // Todos los usuarios que tengan productos - has recibe el nombre de una relación de Seller
        $builder->has('products')->orderBy('id', 'asc'); // Todos los usuarios que tengan productos - has recibe el nombre de una relación de Seller
    }
}
