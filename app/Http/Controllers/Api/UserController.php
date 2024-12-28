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
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object")
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

    public function preferences(Request $request)
    {
        return PreferenceResource::collection($request->user()->preferences);
    }

    public function savePreferences(SavePreferenceRequest $request)
    {
        $request->user()->preferences()->delete();

        foreach ($request->preferences as $preference) {
            $request->user()->preferences()->create($preference);
        }

        return PreferenceResource::collection($request->user()->preferences)->additional(['message' => 'Preferences saved successfully.']);
    }

}
