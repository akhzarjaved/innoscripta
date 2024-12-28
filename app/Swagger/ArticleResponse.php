<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ArticleResponse",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Article")
 *     ),
 *     @OA\Property(
 *         property="links",
 *         ref="#/components/schemas/PaginationLinks"
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         ref="#/components/schemas/PaginationMeta"
 *     )
 * )
 */
class ArticleResponse
{

}
