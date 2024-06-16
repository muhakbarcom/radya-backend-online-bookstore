<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Order",
 *     title="Order",
 *     description="Order model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the order",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who placed the order",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status of the order",
 *         example="completed"
 *     ),
 *     @OA\Property(
 *         property="total_price",
 *         type="number",
 *         format="float",
 *         description="Total price of the order",
 *         example=150.00
 *     ),
 *     @OA\Property(
 *         property="order_number",
 *         type="string",
 *         description="Unique order number",
 *         example="ORDER-123456"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Order created at",
 *         example="2021-08-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Order updated at",
 *         example="2021-08-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderItem"),
 *         description="List of order items"
 *     )
 * )
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'order_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
