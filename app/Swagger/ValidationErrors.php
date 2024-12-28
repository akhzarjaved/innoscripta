<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ValidationErrors",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Validation error message"),
 *     @OA\Property(property="errors", type="object")
 * )
 */
class ValidationErrors
{

}
