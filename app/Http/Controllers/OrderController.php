<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderProducts;
use App\Models\Orders;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use KS\PromptPay;

class OrderController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        $array_data = (array)$data;

        $dataAll = $request->json()->all();
        $products = $dataAll['product'];

        $validator = Validator::make($array_data, [
            'product.*.id' => 'required',
            'product.*.amount' => 'required',
            'product.*.price' => 'required',
            'type_shipping' => 'required',
            'address' => 'required',

        ], [
            "product.*.id" => "กรุณาเลือกสินค้า",
            "product.*.amount" => "กรุณากรอกจำนวน",
            "product.*.price" => "กรุณากรอกราคา",
            'type_shipping.required' => 'กรุณาเลือกประเภทการจัดส่ง',
            'address.required' => 'ไม่พบID Address',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $datetime = new DateTime();

        $order = new Orders();
        $order->user_id = Auth::user()->id;
        $order->status = "Check Payment";
        // $order->type_shipping = "รับหน้าร้าน";
        $order->type_shipping = $data->type_shipping;
        $order->order_date = $datetime->format('d-M-Y');


        if ($data->address) {
            $order->address_id = $data->address;
        }

        $order->save();

        foreach ($products as  $product) {

            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $product['id'];
            $order_item->amount = $product['amount'];
            $order_item->price = $product['price'];
            $order_item->save();
        }

        $orderId = (string)$order->id;

        $token = env("LINE_CHANNAL_ACCECT_TOKEN");
        $url = "https://api.line.me/v2/bot/message/push";
        $date = Carbon::now()->format('d/M/Y');
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
                                "url" => "https://i.ibb.co/4stMkjh/01.png",
                                "size" => "full",
                                "aspectRatio" => "20:8",
                                "aspectMode" => "cover",
                                "action" => [
                                    "type" => "uri",
                                    "uri" => "http://linecorp.com/"
                                ]
                            ],
                            "body" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "weight" => "bold",
                                        "size" => "xl",
                                        "contents" => [
                                            [
                                                "type" => "span",
                                                "text" => "ได้รับคำสั่งซื้อ",
                                                "color" => "#A58151"
                                            ]
                                        ],
                                        "align" => "center"
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "ขอบคุณสำหรับการสั่งซื้อสินค้า",
                                        "margin" => "md",
                                        "size" => "sm",
                                        "align" => "center",
                                        "color" => "#aaaaaa"
                                    ],
                                    [
                                        "type" => "separator",
                                        "margin" => "xxl"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "margin" => "none",
                                        "spacing" => "sm",
                                        "contents" => [
                                            [
                                                "type" => "box",
                                                "layout" => "baseline",
                                                "spacing" => "sm",
                                                "contents" => [
                                                    [
                                                        "type" => "text",
                                                        "text" => "หมายเลขคำสั่งซื้อ :",
                                                        "color" => "#aaaaaa",
                                                        "size" => "sm",
                                                        "flex" => 5,
                                                        "align" => "end"
                                                    ],
                                                    [
                                                        "type" => "text",
                                                        "text" => $orderId,
                                                        "wrap" => true,
                                                        "color" => "#666666",
                                                        "size" => "sm",
                                                        "flex" => 5
                                                    ]
                                                ],
                                                "margin" => "xxl"
                                            ],
                                            [
                                                "type" => "box",
                                                "layout" => "baseline",
                                                "spacing" => "sm",
                                                "contents" => [
                                                    [
                                                        "type" => "text",
                                                        "text" => "วันที่ชำระเงิน :",
                                                        "color" => "#aaaaaa",
                                                        "size" => "sm",
                                                        "flex" => 5,
                                                        "align" => "end"
                                                    ],
                                                    [
                                                        "type" => "text",
                                                        "text" => $date,
                                                        "wrap" => true,
                                                        "color" => "#666666",
                                                        "size" => "sm",
                                                        "flex" => 5
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            "footer" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "spacing" => "sm",
                                "contents" => [
                                    [
                                        "type" => "button",
                                        "style" => "link",
                                        "height" => "sm",
                                        "action" => [
                                            "type" => "uri",
                                            "label" => "WEBSITE",
                                            "uri" => "https://waiwan.com"
                                        ],
                                        "color" => "#A58151"
                                    ]
                                ],
                                "flex" => 0
                            ],
                        ],
                    ],
                ],
            ]
        );

        if ($responseNotify->failed()) {
            return response($responseNotify, 400);
        }

        return response()->json(["id" => $orderId], 200);
    }


    public function show($id)
    {
        //
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
