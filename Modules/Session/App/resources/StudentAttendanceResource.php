<?php

namespace Modules\Session\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Student\App\resources\StudentResource;

class StudentAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            // 'session' => [
            //     'id' => $this->id,
            //     'day' => $this->day,
            //     'semester' => $this->semester,
            //     'session_number' => $this->session_number,
            //     'year' => $this->year,
            //     'class' => $this->class->name,
            //     'grade' => $this->class->grade->name,
            //     'school' => $this->class->school->name,
            //     'subject' => $this->subject->name,
            //     'teacher' => $this->teacher ? $this->teacher->teacher->name : null,
            //     'created_at' => $this->created_at->format('Y-m-d h:i A'),
            //     'updated_at' => $this->updated_at->format('Y-m-d h:i A'),
            // ],
'students' => StudentResource::collection($this->resource),
        ];
    }
}

