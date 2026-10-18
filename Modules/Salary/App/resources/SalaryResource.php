<?php

namespace Modules\Salary\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        $data['total'] = $data['salary'] + $data['bonus'] - $data['deduction'];
        return $data;
    }
}
