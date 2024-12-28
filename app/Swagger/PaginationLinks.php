<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     @OA\Property(property="first", type="string", example="api/articles?page=1"),
 *     @OA\Property(property="last", type="string", example="api/articles?page=1"),
 *     @OA\Property(property="prev", type="string", nullable=true, example=null),
 *     @OA\Property(property="next", type="string", nullable=true, example=null)
 * )
 */
class PaginationLinks
{

}
