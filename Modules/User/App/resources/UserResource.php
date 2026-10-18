<?php

namespace Modules\User\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data =  [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "image" => $this->image,
            "school_id" => $this->school_id,
            "remember_token" => $this->remember_token,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at->format('Y-m-d h:i A'),
            "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
            // 'role' => $this->role,
            'role' => count($this->roles->pluck('name')) == 1 ? $this->roles->pluck('name')->first() : $this->roles->pluck('name'),
        ];
        if ($this->hasRole('Student')) {
            $data['student'] = $this->student->load(['grade.gradeCategory','studentFees']);
        }
        return $data;
    }
}
