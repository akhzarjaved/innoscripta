<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="CeeDee Lamb is out for the year."),
 *     @OA\Property(property="description", type="string", example="The Cowboys receiver finishes the season with 101 receptions for 1,194 and six touchdowns."),
 *     @OA\Property(property="url", type="string", format="url", example="https://www.nbcsports.com"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-12-26 21:31:22"),
 *     @OA\Property(
 *         property="source",
 *         ref="#/components/schemas/Source"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         ref="#/components/schemas/Category"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         ref="#/components/schemas/Author"
 *     )
 * )
 */
class Article
{

}
