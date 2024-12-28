# Разбор проекта на Laravel 11

- В проекте имеется обработка ```Exception Handler``` для того чтобы поменять рендеринг исключений
``class Handler extends ExceptionHandler``
- В проекте имеется таблица с транзакциями
```php 
     Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->unsigned();
            $table->bigInteger('buyer_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('buyer_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
        });
```
Сделано согласно документации https://laravel.com/docs/11.x/errors#rendering-exceptions
Причем обращения от транзакций к товарам и категориям идут такие
```php 
$categories = $buyer->transactions()->with('product.categories')
->get()
->pluck('product.categories')
->collapse() // Juntar todas las listas de categorías en una sola
->unique('id')
->values(); 
```
- Выборка ассоциативного массива с параметрами 
```php 
$category->fill($request->only([ // fill asigna datos a los atributos del modelo en forma masiva - only en Laravel 5.5 o superior
            'name',
            'description',
        ]));
```
- API реализован интересно. Имеется трейт ``ApiResponser`` для форматирования ответа.
В нем реализованы методы, которые вызываются при формировании ответа:
```php
  private function successResponse($data, $code);
  public function errorResponse($message, $code);
  protected function showAll(Collection $collection, $code = 200);
  protected function showOne(Model $instance, $code = 200);  
```
- Для моделей реализованы глобальные и локальные скоупы 
Например, ``BuyerScope``. Это скоуп для того чтобы определять покупателей и делается это 
опеределением имеются ли связаннаые транзакции ``$builder->hasTransactions()->idAscending();``. 
Тут  ``idAscending`` - это локальный скоуп который определен в модели ``Buyer`` 
```php 
public function scopeIdAscending($query)
```
- В проекте имеется ```Seeder```. Имеется чуть сложное место в ``Seeder``, где сначала создаются продукты, и далее идет
цикл по продуктам, в теле цикла продукты привязываются к категориям.
```php 
   Product::factory($cantidadProductos)
            ->create()
            ->each(function ($producto) {
                $categorias = Category::all()->random(mt_rand(1, 5))->pluck('id');

                $producto->categories()->attach($categorias);
            });

        Transaction::factory($cantidadTransacciones)->create();
```
- В конфигурациях логов имеется ссылка на ``papertrail``

