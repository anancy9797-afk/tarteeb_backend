<?php

namespace Modules\Subject\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'grade' => $this->grade->name,
            'grade_category' => $this->grade->gradeCategory->name,
            'school' => $this->school->name,
            'semester' => $this->semester,
            "created_at" => $this->created_at->format('Y-m-d h:i A'),
            "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
        ];
    }
}
