<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatacenterResource;
use App\Http\Resources\ProviderResource;
use App\Models\Datacenter;
use App\Models\Provider;
use App\Models\Region;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $response = ProviderResource::collection(Provider::all());
        return $response;
    }


}
