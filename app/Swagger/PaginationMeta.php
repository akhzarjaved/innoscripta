<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=1),
 *     @OA\Property(
 *         property="links",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="url", type="string", nullable=true, example=null),
 *             @OA\Property(property="label", type="string", example="Previous"),
 *             @OA\Property(property="active", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Property(property="path", type="string", example=""),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="to", type="integer", example=1),
 *     @OA\Property(property="total", type="integer", example=1)
 * )
 */
class PaginationMeta
{

}
