<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatacenterResource;
use App\Models\Datacenter;
use App\Models\Provider;
use App\Models\Region;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class DatacenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $key = $this->buildCacheKey($request);

//        if(Cache::has($key)) {
//            return Cache::get($key);
//        }

        $collection = Datacenter::where('provider_id', '!=', 8);
        if($request->has('provider') && !is_null($request->input('provider'))) {
            $provider = Provider::where('code', $request->input('provider'))->firstOrFail();
            $collection->where('provider_id', $provider->id);
        }
        if($request->has('region') && !is_null($request->input('region'))) {
            $region = Region::where('name', $request->input('region'))->firstOrFail();
            $collection->whereHas('regions', function (Builder $query) use ($region) {
                $query->where('regions.id', '=', $region->id);
            });
        }

        $response = DatacenterResource::collection($collection->with('provider')->with('regions')->get());
        Cache::put($key, $response, 600);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return DatacenterResource
     */
    public function show(int $id): DatacenterResource
    {
        if(Cache::has("dc-".$id)) {
            return Cache::get("dc-".$id);
        }

        $response = new DatacenterResource(Datacenter::findOrFail($id));
        Cache::put("dc-".$id, $response, 600);

        return $response;
    }

    protected function buildCacheKey(Request $request) : string {
        $key = "dc";
        if($request->has('provider') && !is_null($request->input('provider'))) {
            $key .= "-provider-".$request->input('provider');
        }
        if($request->has('region') && !is_null($request->input('region'))) {
            $key .= "-region-".$request->input('region');
        }

        return $key;
    }
}
