<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ServiceCategoryResource;

class ProviderProfileResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'about_me' => $this->about_me,
            'email' => $this->email,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'iso_code' => $this->iso_code,
            'phone_code' => $this->phone_code,
            'phone_number' => $this->phone_number,
            'zip_code' => $this->zip_code,
            'profile_file' => $this->profile_file,
            'home_services' =>  (!empty($this->profile_file) && !empty($this->profile_identity_file)),
            'profile_identity_video' => $this->profile_identity_video,
            'profile_identity_file' => $this->profile_identity_file,
            'account_type' => $this->workProfile->account_type,
            'service_category_id' => $this->workProfile->service_category_id,
            'service_category' => $this->workProfile->serviceCategory,
            'subCategory' => $this->workProfile->getSubServices(),
           
        ];
    }
}
