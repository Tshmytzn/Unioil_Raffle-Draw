<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Jobs\SendEntryCoupon;
use App\Models\QrCode;
use App\Http\Services\Tools;
use App\Models\ProductList;
use App\Http\Services\Magic;
use App\Models\RetailStore;
use App\Models\Event;
use App\Models\RegionalCluster;
use Illuminate\Http\JsonResponse;

class CustomerRegistration extends Controller
{
    public function register(Request $req): JsonResponse{
        $qrCode = QrCode::where('code', $req->qr_code)->where('qr_id', $req->unique_identifier)->first();

        if(!$qrCode){
            return response()->json(['success'=> false, 'message'=> 'QR Code Credentials is not found in the db']);
        }

        $qrStatus = QrCode::where('code', $req->qr_code)->where('qr_id', $req->unique_identifier)->where('status', 'used')->first();

        if($qrStatus){
            return response()->json(['success'=> false, 'message'=> 'QR Code is not available anymore']);
        }

        $retailStore = RetailStore::where('rto_code', $req->store_code)->first();

        if(!$retailStore){
            return response()->json(['success'=> false, 'message'=> 'Retail Store Code is invalid please confirm the code to the store owner']);
        }

        $checkClusterId = RegionalCluster::where('cluster_id', $retailStore->cluster_id)->where('cluster_status', 'Disable')->first();

        if($checkClusterId){
            return response()->json(['success'=> false, 'message'=>'This store is not participating in the raffle']);
        }

        $currentActiveEvent = Event::where('event_status', Magic::ACTIVE_EVENT)->first();

        if(!$currentActiveEvent){
            return response()->json(['success'=> false, 'message'=> 'There is no current promo event available for this entry']);
        }

        $customer = new Customers();

        $customer->full_name = $req->fullname;
        $customer->age = $req->age;
        $customer->region = $req->region;
        $customer->province = $req->province;
        $customer->city = $req->city;
        $customer->brgy = $req->baranggay;
        $customer->street = $req->street;
        $customer->mobile_number = $req->mobile_number;
        $customer->email = $req->email_address;
        $customer->qr_id = $qrCode->qr_id;
        $customer->product_purchased = $req->product;
        $customer->store_id = $retailStore->store_id;
        $customer->event_id = $currentActiveEvent->event_id;
        $customer->save();

        $productEntry = ProductList::where('product_id', $req->product)->first();

        if(!$productEntry){
            return response()->json(['success'=> false, 'message' => 'No Product Found']);
        }

        if($productEntry->entries == 1){
            $code = Tools::CreateEntries($customer->customer_id, $req->unique_identifier, $req->store_code);
            if(!empty($req->email_address)){
                SendEntryCoupon::dispatch(Magic::RAFFLE_ENTRY_SINGLE, [$code], $req->email_address);
            }

        }else{
            $code1 = Tools::CreateEntries($customer->customer_id, $req->unique_identifier, $req->store_code);
            $code2 = Tools::CreateEntries($customer->customer_id, $req->unique_identifier, $req->store_code);

            if(!empty($req->email_address)){
                SendEntryCoupon::dispatch(Magic::RAFFLE_ENTRY_DOUBLE, [$code1, $code2], $req->email_address);
            }
        }

        $qrCode->update([
            'status' => 'used'
        ]);

        return response()->json(['success'=> true, 'customer_id'=> $customer->customer_id, 'entry'=> $productEntry->entries]);
    }

    public function checkretailstore(Request $req) : JsonResponse{
        $store = RetailStore::where('rto_code', $req->rto_code)->first();

        if(!$store){
            return response()->json(['success'=> false, 'message'=> 'The retail code is invalid. Please verify the entered code with the store owner.']);
        }

        return response()->json(['success'=> true, 'message'=> 'Verify Retail Code', 'store'=>$store]);
    }
}
