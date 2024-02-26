<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use KS\PromptPay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PaymentsController extends Controller
{

    public function index()
    {
        //
    }

    public function getPromptPay(Request $request)
    {

        $data = json_decode($request->getContent());

        $array_data = (array) $data;

        $validator = Validator::make($array_data, [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $order_id = $request->input('order_id');

        $totalPriceSum = (float)OrderItem::where('order_id', $order_id)
            ->sum(DB::raw('price * amount'));

        // dd($totalPriceSum);

        $priceShiping = (float)DB::table('order_items')
            ->select('products.typeShipping')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', '=', $request->order_id)
            ->orderBy('products.typeShipping', 'desc')
            ->first()->typeShipping;


        $totalPrice = $totalPriceSum + $priceShiping;

        $pp = new PromptPay();
        $target = '088-656-5433';
        $promptPay = $totalPrice;

        $payload = $pp->generatePayload($target, $promptPay);

        return response()->json(['payload' => $payload, 'amount' => $promptPay]);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $array_data = (array) $data;

        $validator = Validator::make($array_data, [
            'order_id' => 'required|exists:orders,id',
            'slip_img' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $order_id = $request->input('order_id');

        $totalPriceSum = (float)OrderItem::where('order_id', $order_id)
            ->sum(DB::raw('price * amount'));

        // dd($totalPriceSum);

        $priceShiping = (float)DB::table('order_items')
            ->select('products.typeShipping')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', '=', $request->order_id)
            ->orderBy('products.typeShipping', 'asc')
            ->first()->typeShipping;


        $totalPrice = $totalPriceSum + $priceShiping;

        // dd($totalPrice);

        $payment = new Payment();
        $payment->user_id = Auth::id();
        $payment->order_id = $data->order_id;
        $payment->price = $totalPrice;
        $payment->status = "Paid";
        $payment->slip_img = $data->slip_img;
        $payment->save();

        $orderId = (string)$order_id;
        $totalPrices = (string)$totalPrice;

        $date = Carbon::now()->format('d/M/Y');

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
                                "url" => "https://i.ibb.co/jhV2qFY/02.png",
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
                                                "text" => "คุณได้แจ้งชำระเงินแล้ว !",
                                                "color" => "#A58151"
                                            ]
                                        ],
                                        "align" => "center"
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "กรุณารอยืนยันการชำระเงิน",
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
                                                        "text" => "ยอดรวม :",
                                                        "color" => "#aaaaaa",
                                                        "size" => "sm",
                                                        "flex" => 5,
                                                        "align" => "end"
                                                    ],
                                                    [
                                                        "type" => "text",
                                                        "text" => $totalPrices,
                                                        "wrap" => true,
                                                        "color" => "#666666",
                                                        "size" => "sm",
                                                        "flex" => 5
                                                    ]
                                                ]
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


        return response()->json(["message" => "บันทึกสำเร็จ"], 200);
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
