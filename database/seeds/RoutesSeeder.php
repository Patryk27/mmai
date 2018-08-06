<?php

use App\Routes\Models\Route;

class RoutesSeeder extends Seeder
{

    /**
     * @return void
     *
     * @throws Throwable
     */
    public function run(): void
    {
        $this->createRedirection('/', 'en');
    }

    /**
     * Creates a redirection from any given URL ($fromUrl) onto an already
     * existing route ($toUrl).
     *
     * @param string $fromUrl
     * @param string $toUrl
     * @return void
     *
     * @throws Throwable
     */
    private function createRedirection(string $fromUrl, string $toUrl): void
    {
        $toUrlModel = Route::where('url', $toUrl)->firstOrFail();

        Route::buildFor($fromUrl, $toUrlModel)
            ->saveOrFail();
    }

}