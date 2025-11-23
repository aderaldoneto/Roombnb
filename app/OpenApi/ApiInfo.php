<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Roombnb API",
 *     version="1.0.0",
 *     description="API para listagem de rooms, criação de reservas e listagem de reservas.",
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Ambiente local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class ApiInfo {}
