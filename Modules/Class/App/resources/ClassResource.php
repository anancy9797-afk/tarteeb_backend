<?php

namespace Modules\Class\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'school' => $this->school->name,
            'grade' => $this->grade->name,
            'grade_category' => $this->grade->gradeCategory->name,
            'max_students' => $this->max_students,
            'session_number' => $this->session_number,
            "created_at" => $this->created_at->format('Y-m-d h:i A'),
            "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
        ];
    }
}
