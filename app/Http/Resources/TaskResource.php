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
        $this->assigned_to ? $full_name ="{$this->assignedUser->firstName} {$this->assignedUser->lastName}" : $full_name ="Not Assigned to anyOne ";
       
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
