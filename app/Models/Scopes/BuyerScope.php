<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BuyerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // $builder->has('transactions'); // Todos los usuarios que tengan transacciones - has recibe el nombre de una relación de Buyer
        $builder->hasTransactions()->idAscending(); // Usa el local scope
    }
}
