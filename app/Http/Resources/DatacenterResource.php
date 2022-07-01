<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DatacenterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'provider_code_api' => $this->provider_code_api,
            'city' => $this->city,
            'country_code' => $this->country_code,
            'facilities' => $this->facilities,
            'lat' => $this->lat,
            'long' => $this->long,
            'planned' => $this->planned,
            'special' => $this->special,
            'provider' => new ProviderResource($this->provider),
            'regions' => RegionResource::collection($this->regions)
        ];
    }
}
