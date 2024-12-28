<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SavePreferenceRequest;
use App\Http\Resources\PreferenceResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Current user",
     *     description="Check the current logged-in user.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *           response=401,
     *           description="Unauthenticated",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Unauthenticated.")
     *           )
     *       )
     * )
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * @OA\Get(
     *     path="/api/user/preferences",
     *     summary="Current user article preferences",
     *     description="Check the current logged-in user article preferences.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="type", type="string", example="category"),
     *                      @OA\Property(property="value", type="integer", example=1)
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *           response=401,
     *           description="Unauthenticated",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Unauthenticated.")
     *           )
     *       )
     * )
     */
    public function preferences(Request $request)
    {
        return PreferenceResource::collection($request->user()->preferences);
    }

    /**
     * @OA\Post(
     *     path="/api/user/preferences",
     *     summary="Save current user article preferences",
     *     description="Update the current logged-in user article preferences.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="preferences",
     *                  type="array",
     *                  description="User preferences",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property( property="type", type="string", example="category" ),
     *                      @OA\Property( property="value", type="integer", example=1 )
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Preferences saved successfully."),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="type", type="string", example="category"),
     *                      @OA\Property(property="value", type="integer", example=1)
     *                 )
     *             )
     *          )
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation errors",
     *          @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *      )
     * )
     */
    public function savePreferences(SavePreferenceRequest $request)
    {
        $request->user()->preferences()->delete();

        foreach ($request->preferences as $preference) {
            $request->user()->preferences()->create($preference);
        }

        return PreferenceResource::collection($request->user()->preferences)->additional(['message' => 'Preferences saved successfully.']);
    }

}
