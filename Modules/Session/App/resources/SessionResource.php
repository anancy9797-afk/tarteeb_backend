<?php

namespace Modules\Session\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'session_number' => $this->session_number,
            'semester' => $this->semester,
            'year' => $this->year,
            'class' => $this->class->name,
            'subject' => $this->subject->name,
            'teacher' => $this->teacher ? $this->teacher->teacher->name : null,
            'school' => $this->school->name,
            "created_at" => $this->created_at->format('Y-m-d h:i A'),
            "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
        ];
    }
}
