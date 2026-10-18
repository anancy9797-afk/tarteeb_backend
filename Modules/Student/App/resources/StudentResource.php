<?php

namespace Modules\Student\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'identity_number' => $this->identity_number,
            'parent_email' => $this->parent_email ?? null,
            'parent_phone' => $this->parent_phone,
            'gender' => $this->gender == 'm' ? 'Male' : 'Female',
            'grade' => $this->grade->name,
            'grade_category' => $this->grade->gradeCategory->name,
            'class' => $this->class->name ?? null,
            'school' => $this->school->name,
            'is_active' => $this->is_active,
            'is_graduated' => $this->is_graduated,
              'created_at' => $this->created_at?->format('Y-m-d h:i A'),
            'updated_at' => $this->updated_at?->format('Y-m-d h:i A'),
            // 'created_at' => $this->created_at->format('Y-m-d h:i A'),
            // 'updated_at' => $this->updated_at->format('Y-m-d h:i A'),
        ];

        if (isset($this->is_attend)) {
            $data['is_attend'] = $this->is_attend == 0 ? 0 : 1;
        }

        return $data;
    }
}
