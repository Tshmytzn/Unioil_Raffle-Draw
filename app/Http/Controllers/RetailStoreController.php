<?php

namespace App\Http\Controllers;

use App\Http\Services\Tools;
use Illuminate\Http\Request;
use App\Models\RegionalCluster;
use App\Models\RetailStore;

class RetailStoreController extends Controller
{

    public function addcluster(request $request){

        $data =  new RegionalCluster();
        $data->cluster_name = $request->cluster_name;
        $data->save();

        return response()->json(['success' => true , 'message'=>'Regional cluster successfully added', 'reload'=> 'LoadAll']);
    }

    public function getcluster()
    {
        $data = RegionalCluster::orderBy('created_at', 'asc')->get(); // Order by 'created_at' in ascending order
        return response()->json(['data' => $data]);
    }


    public function clusterstatus(request $request){
        $data = RegionalCluster::find($request->id);
        $store = RetailStore::where('cluster_id',$data->cluster_id)->get();
        if (!$store->isEmpty()) {
            foreach ($store as $retail) {
                $retail->delete();
            }
        }
        $data->delete();
        return response()->json(['success' => true , 'message'=>'Cluster status successfully delete', 'reload'=> 'LoadAll']);
    }

    public function addstore(request $request){
        $data = new RetailStore();
        $data->cluster_id = $request->cluster_id;
        $data->region_name = $request->region_name;
        $data->city_name = $request->city_name;
        $data->store_name = $request->store_name;
        $data->store_code = $request->store_code;
        $data->save();
        return response()->json(['success' => true, 'message' => 'Store status successfully added', 'reload' => 'LoadAll']);
    }

    public function getallstore(){
        $data = RetailStore::join('regional_cluster', 'retail_store.cluster_id', '=', 'regional_cluster.cluster_id')
        ->select('regional_cluster.cluster_name', 'retail_store.*')
        ->get();
        return response()->json(['data'=>$data]);
    }

    public function removeretailstore(request $reqeust){
        $data = RetailStore::find($reqeust->id);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Store status successfully delete']);
    }

    public function updatestore(request $request)
    {
        $data = RetailStore::where('store_id',$request->store_id)->first();
        $data->cluster_id = $request->cluster_id;
        $data->region_name = $request->region_name;
        $data->city_name = $request->city_name;
        $data->store_name = $request->store_name;
        $data->store_code = $request->store_code;
        $data->save();
        return response()->json(['success' => true, 'message' => 'Store status successfully update', 'reload' => 'LoadAll']);
    }

    public function uploadcsv(Request $req){
        $csv = $req->csv_file;

        $csvData = Tools::readCSV($csv);


        array_shift($csvData);
        foreach($csvData as $data){
            $store = new RetailStore();

            if(!empty($data[0]) && !empty($data[1]) && !empty($data[2]) && !empty($data[3]) && !empty($data)){
                $store->cluster_id = $req->cluster;
                $store->area = $data[0];
                $store->address = $data[1];
                $store->distributor = $data[2];
                $store->retail_station = $data[3];
                $store->rto_code = $data[4];
                $store->save();
            }

        }

        return response()->json(['success'=> true, 'message'=> 'All data are uploaded in the database']);
    }
}
