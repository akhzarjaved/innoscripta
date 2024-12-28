<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleSearchRequest;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class ArticleController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Retrieve articles",
     *     description="Listing all the articles based on search criteria.",
     *     tags={"Article"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="The page number for pagination",
     *          required=false,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Parameter(
     *          name="category",
     *          in="query",
     *          description="Category ID to filter by",
     *          required=false,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Parameter(
     *          name="source",
     *          in="query",
     *          description="Source ID to filter by",
     *          required=false,
     *          @OA\Schema(type="integer", example=129)
     *      ),
     *      @OA\Parameter(
     *          name="keyword",
     *          in="query",
     *          description="Keyword to search for in articles",
     *          required=false,
     *          @OA\Schema(type="string", example="CeeDee")
     *      ),
     *      @OA\Parameter(
     *          name="date",
     *          in="query",
     *          description="Date to filter by",
     *          required=false,
     *          @OA\Schema(type="string", format="date", example="2024-12-26")
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ArticleResponse")
     *      ),
     *     @OA\Response(
     *           response=401,
     *           description="Unauthenticated",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Unauthenticated.")
     *           )
     *       ),
     *     @OA\Response(
     *           response=422,
     *           description="Validation errors",
     *           @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *       )
     * )
     */
    public function search(
        ArticleRepositoryInterface $articleRepository,
        ArticleSearchRequest $request
    )
    {
        return ArticleResource::collection($articleRepository->search($request));
    }

    /**
     * @OA\Get(
     *     path="/api/preferred-articles",
     *     summary="Preferred articles",
     *     description="Listing all the user preferred articles.",
     *     tags={"Article"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="The page number for pagination",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ArticleResponse")
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
    public function preferred(
        ArticleRepositoryInterface $articleRepository
    )
    {
        return ArticleResource::collection($articleRepository->searchPreferred());
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Article detail",
     *     description="show the details for requested article.",
     *     tags={"Article"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="The id of article",
     *           required=true,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/Article"
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
    public function details(
        ArticleRepositoryInterface $articleRepository,
        $articleId
    )
    {
        $article = $articleRepository->find($articleId);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        $article->load(['author', 'category', 'source']);
        return new ArticleResource($article);
    }
}
