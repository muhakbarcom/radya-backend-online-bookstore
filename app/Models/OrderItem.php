<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     title="OrderItem",
 *     description="OrderItem model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the order item",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="order_id",
 *         type="integer",
 *         description="ID of the order",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="book_id",
 *         type="integer",
 *         description="ID of the book",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantity of the book",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Price of the book",
 *         example=50.00
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Order item created at",
 *         example="2021-08-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Order item updated at",
 *         example="2021-08-01T00:00:00Z"
 *     )
 * )
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'book_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
