<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $isArray = is_array($resource);

        return [
            'id' => $isArray ? ($resource['id'] ?? null) : $resource->id,
            'title' => $isArray ? ($resource['title'] ?? null) : $resource->title,
            'content' => $isArray ? ($resource['content'] ?? null) : $resource->content,
            'published_at' => $isArray ? ($resource['published_at'] ?? null) : $resource->published_at,
            'user' => $isArray ? ($resource['user'] ?? null) : $this->whenLoaded('user'),
        ];
    }
}
