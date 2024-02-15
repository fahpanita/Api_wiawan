<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Catagories;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class HomeController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        return response()->json($user, 200);
    }


    public function getSeller()
    {
        $data = DB::table('orders')
            ->select(
                'orders.id as order_id',
                'orders.user_id',
                'orders.address_id',
                'orders.status',
                'orders.type_shipping',
                'orders.order_date',
                DB::raw('GROUP_CONCAT(DISTINCT order_items.id) as order_item_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT order_items.product_id) as product_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT order_items.amount) as amounts'),
                DB::raw('GROUP_CONCAT(DISTINCT order_items.price) as order_item_prices'),
                DB::raw('GROUP_CONCAT(DISTINCT payments.id) as payment_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT payments.user_id) as payment_user_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT payments.price) as payment_prices'),
                DB::raw('GROUP_CONCAT(DISTINCT payments.status) as payment_statuses'),
                DB::raw('GROUP_CONCAT(DISTINCT payments.slip_img) as slip_imgs'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.id) as address_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.name) as address_names'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.street) as streets'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.district) as districts'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.subdistrict) as subdistricts'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.province) as provinces'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.zip_code) as zip_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT addresses.phone) as phones'),
                DB::raw('GROUP_CONCAT(DISTINCT products.id) as product_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT products.name) as product_names')
            )
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy(
                'orders.id',
                'orders.user_id',
                'orders.address_id',
                'orders.status',
                'orders.type_shipping',
                'orders.order_date'
            )->orderBy('orders.id', 'DESC')
            ->get();


        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        //
    }

    public function getCategory()
    {
        $categoryId = 2;

        $products = Products::select('products.*')
            ->join('products_cataories', 'products.id', '=', 'products_cataories.product_id')
            ->join(DB::raw("(WITH RECURSIVE category_tree AS (
            SELECT id, name, parent_id
            FROM catagories
            WHERE id = {$categoryId}
            UNION
            SELECT c.id, c.name, c.parent_id
            FROM catagories c
            JOIN category_tree ct ON c.parent_id = ct.id
          )
          SELECT * FROM category_tree) as selected_categories"), function ($join) {
                $join->on('selected_categories.id', '=', 'products_cataories.cataory_id');
            })
            ->get();

        dd($products);
    }


    public function show($id)
    {
        //
    }

    public function getConfirmOrder(Request $request)
    {

        $token = env("LINE_CHANNAL_ACCECT_TOKEN");
        $url = "https://api.line.me/v2/bot/message/push";
        $responseNotify = Http::withHeaders([
            'Authorization' => "Bearer " . $token,
        ])->post(
            $url,
            [
                "to" => Auth::user()->line_id,
                "messages" => [
                    [
                        "type" => "flex",
                        "altText" => "This is a Flex Message",
                        "contents" => [
                            "type" => "bubble",
                            "hero" => [
                                "type" => "image",
                                "url" => "https://img2.pic.in.th/pic/Group-5392.png",
                                "size" => "full",
                                "aspectRatio" => "20:13",
                                "aspectMode" => "cover",
                                "action" => [
                                    "type" => "uri",
                                    "uri" => "http://linecorp.com/",
                                ],
                            ],
                            "body" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "ยืนยันการสั่งซื้อ",
                                        "size" => "xl",
                                        "weight" => "bold",
                                        "align" => "center",
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "กำลังเตรียมการจัดส่ง!",
                                        "color" => "#999999",
                                        "align" => "center",
                                    ],
                                    ["type" => "separator"],
                                    [
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "margin" => "lg",
                                        "spacing" => "sm",
                                        "contents" => [
                                            [
                                                "type" => "box",
                                                "layout" => "baseline",
                                                "spacing" => "sm",
                                                "contents" => [
                                                    [
                                                        "type" => "text",
                                                        "text" => "หมายเลขคำสั่งซื้อ",
                                                        "color" => "#aaaaaa",
                                                        "size" => "sm",
                                                        "flex" => 4,
                                                    ],
                                                    [
                                                        "type" => "text",
                                                        "text" => "12345",
                                                        "wrap" => true,
                                                        "color" => "#666666",
                                                        "size" => "sm",
                                                        "flex" => 5,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            "footer" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "spacing" => "sm",
                                "contents" => [
                                    [
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "contents" => [],
                                        "margin" => "sm",
                                    ],
                                ],
                                "flex" => 0,
                            ],
                        ],
                    ],
                ],
            ],

        );

        if ($responseNotify->failed()) {
            return response($responseNotify, 400);
        }


        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
