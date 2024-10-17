<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $full_name = "{$this->firstName} {$this->lastName}";
        return
        [
            'title'           =>  $this->title,            
            'Description'     => $this->description,
            'Type'            => $this->type,
            'Status'          => $this->status,
            'Priority'        => $this->priority,
            'Due Date'        => $this->due_date,
            'Assigned To'     => $full_name,
        ];
    }
}
