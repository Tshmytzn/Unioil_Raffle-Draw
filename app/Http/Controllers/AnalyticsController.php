<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\ProductList;
use App\Models\Event;


class AnalyticsController extends Controller
{
    public function getEventData($eventId)
    {
        $activeEvent = Event::where('event_status', 'Active')->first();

        if (!$activeEvent) {
            return response()->json([
                'success' => false,
                'message' => 'No active event found.',
                'eventData' => null,
            ]);
        }

        $customers = Customers::where('event_id', $eventId)->get();
        $countFull = 0;
        $countSemi = 0;
        foreach ($customers as $customer) {
            $products = ProductList::where('product_id', $customer->product_purchased)->first();
            if ($products->entries == 1) {
                $countSemi += 1;
            } else {
                $countFull += 1;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event data fetched successfully.',
            'eventData' => $activeEvent,
            'semiSynthetic' => $countSemi,
            'fullySynthetic' => $countFull,
        ]);
    }


    public function getActiveEvent()
    {
        $activeEvent = Event::where('event_status', 'Active')->first();

        if ($activeEvent) {
            return response()->json([
                'success' => true,
                'eventData' => $activeEvent
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No active event found'
        ]);
    }

    public function entryissuance(Request $req, $filter)
    {
        if ($filter == 'active') {
            $event = Event::where('event_status', 'Active')->first();
        } else {
            $event = Event::where('event_id', $filter)->first();
        }

        $customers = Customers::where('event_id', $event->event_id)
            ->get()
            ->groupBy(function ($date) {
                return $date->created_at->format('Y-m-d');
            });

        $groupedByDate = [];
        foreach ($customers as $date => $records) {
            $groupedByDate[] = [
                'date' => $date,
                'count' => $records->count(),
            ];
        }

        return response()->json($groupedByDate);
    }

    public function entriesbyproducttype(Request $req, $event)
    {
        if ($event == 'active') {
            $eventData = Event::where('event_status', 'Active')->first();
        } else {
            $eventData = Event::where('event_id', $event)->first();
        }

        $customers = Customers::where('event_id', $eventData->event_id)
            ->get()
            ->groupBy(function ($date) {

                return $date->created_at->format('Y-m');
            });

        $groupedByMonth = [];

        foreach ($customers as $month => $records) {
            $f_synthetic = 0;
            $s_synthetic = 0;

            foreach($records as $rec){
                $product = ProductList::where('product_id', $rec['product_purchased'])->first();

                if($product->entries == 1){
                    $s_synthetic++;
                }else{
                    $f_synthetic++;
                }
            }

            $groupedByMonth[] = [
                'month' => $month,
                'count' => $records->count(),
                'fully_synthetic' => $f_synthetic,
                'semi_synthetic' => $s_synthetic
            ];
        }

        return response()->json($groupedByMonth);
    }
}
