<?php

namespace App\Application\Http\Middleware;

use App\Routes\RoutesFacade;
use Closure;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyIsInstalled
{

    /**
     * @var DatabaseConnection
     */
    private $db;

    /**
     * @var RoutesFacade
     */
    private $routesFacade;

    /**
     * @param DatabaseConnection $db
     * @param RoutesFacade $routesFacade
     */
    public function __construct(
        DatabaseConnection $db,
        RoutesFacade $routesFacade
    ) {
        $this->db = $db;
        $this->routesFacade = $routesFacade;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate that the database contains "migrations" table
        $schemaBuilder = $this->db->getSchemaBuilder();

        if (!$schemaBuilder->hasTable('migrations')) {
            return $this->buildResponse([
                'reason' => 'no-database',
            ]);
        }

        // If all conditions are met, proceed
        return $next($request);
    }

    /**
     * @param array $viewData
     * @return Response
     */
    private function buildResponse(array $viewData): Response
    {
        return new Response(
            view('frontend.views.application-not-installed', $viewData)
        );
    }

}
