<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdersRequest;
use DateInterval;
use DateTime;
use Illuminate\View\View;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Enum\CountryCodeIso3166;
use RetailCrm\Api\Enum\Customers\CustomerType;
use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Model\Entity\Orders\Delivery\OrderDeliveryAddress;
use RetailCrm\Api\Model\Entity\Orders\Delivery\SerializedOrderDelivery;
use RetailCrm\Api\Model\Entity\Orders\Items\Offer;
use RetailCrm\Api\Model\Entity\Orders\Items\OrderProduct;
use RetailCrm\Api\Model\Entity\Orders\Order;
use RetailCrm\Api\Model\Entity\Orders\Payment;
use RetailCrm\Api\Model\Entity\Orders\SerializedRelationCustomer;
use RetailCrm\Api\Model\Request\Orders\OrdersCreateRequest;

class OrderController extends Controller
{
    public function index(): View
    {
        $client = SimpleClientFactory::createClient(config('app.retailcrm.url'), config('app.retailcrm.api_key'));

        $productsArray = $client->store->products()->products;


        return view('index', compact('productsArray'));

    }

    public function store(OrdersRequest $ordersRequest)
    {
        $client = SimpleClientFactory::createClient(config('app.retailcrm.url'), config('app.retailcrm.api_key'));

        $request         = new OrdersCreateRequest();
        $order           = new Order();
        $payment         = new Payment();
        $delivery        = new SerializedOrderDelivery();
        $deliveryAddress = new OrderDeliveryAddress();

        $payment->type   = 'bank-card';
        $payment->status = 'paid';
        $payment->amount = 1000;
        $payment->paidAt = new DateTime();

        $deliveryAddress->index      = '344001';
        $deliveryAddress->countryIso = CountryCodeIso3166::RUSSIAN_FEDERATION;
        $deliveryAddress->region     = 'Region';
        $deliveryAddress->city       = 'City';
        $deliveryAddress->street     = 'Street';
        $deliveryAddress->building   = '10';

        $delivery->address = $deliveryAddress;
        $delivery->cost    = 0;
        $delivery->netCost = 0;

        $itemArray = [];

        foreach ($ordersRequest->validated('products') as $key => $product) {
            $offer = new Offer();

            $offer->id = $product;

            $item = new OrderProduct();

            $item->offer = $offer;

            $itemArray += [$key => $item];
        }

        $order->delivery      = $delivery;
        $order->items         = $itemArray;
        $order->payments      = [$payment];
        $order->orderType     = 'main';
        $order->orderMethod   = 'phone';
        $order->countryIso    = CountryCodeIso3166::RUSSIAN_FEDERATION;
        $order->firstName     = $ordersRequest->validated('name');
        $order->lastName      = 'User';
        $order->patronymic    = 'Patronymic';
        $order->phone         = $ordersRequest->validated('phone');
        $order->email         = $ordersRequest->validated('email');
        $order->managerId     = 16;
        $order->customer      = SerializedRelationCustomer::withIdAndType(
            59,
            CustomerType::CUSTOMER
        );
        $order->status        = 'assembling';
        $order->statusComment = 'Assembling order';
        $order->weight        = 1000;
        $order->shipmentStore = 'warehouse';
        $order->shipmentDate  = (new DateTime())->add(new DateInterval('P7D'));
        $order->shipped       = false;
        $order->customFields  = [
            "galka" => false,
            "test_number" => 0,
            "otpravit_dozakaz" => false,
        ];

        $request->order = $order;
        $request->site  = 'moysklad';

        try {
            $response = $client->orders->create($request);
        } catch (ApiExceptionInterface | ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }

        return back()->withErrors('Заказ создан');
    }
}
