<?php

namespace App\Http\Controllers;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Online Bookstore - Radya Labs API Documentation",
 *      description="This is the official API documentation for Online Bookstore - Radya Labs",
 *      @OA\Contact(
 *          email="muhammad.akbar5999@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 * 
 * @OA\Tag(
 *     name="Auth",
 *     description="Login, register, and logout"
 * )
 * @OA\Tag(
 *     name="Books",
 *     description="Master data books"
 * )
 * @OA\Tag(
 *     name="User",
 *     description="Master data users"
 * )
 * @OA\Tag(
 *    name="Inventory",
 *  description="Manage book stock"
 * )
 * @OA\Tag(
 *    name="Shopping Cart",
 * description="Manage shopping cart"
 * )
 * @OA\Tag(
 *   name="Orders",
 * description="Manage orders"
 * )
 */
abstract class Controller
{
    //
}
