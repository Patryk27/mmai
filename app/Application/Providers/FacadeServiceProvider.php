<?php

namespace App\Application\Providers;

use App\Analytics\AnalyticsFacade;
use App\Analytics\AnalyticsFactory;
use App\Analytics\Implementation\Repositories\EloquentEventsRepository;
use App\Analytics\Implementation\Services\Searcher\EloquentRequestEventsSearcher;
use App\Attachments\AttachmentsFacade;
use App\Attachments\AttachmentsFactory;
use App\Attachments\Implementation\Repositories\EloquentAttachmentsRepository;
use App\Languages\Implementation\Repositories\EloquentLanguagesRepository;
use App\Languages\LanguagesFacade;
use App\Languages\LanguagesFactory;
use App\Menus\Implementation\Repositories\EloquentMenuItemsRepository;
use App\Menus\MenusFacade;
use App\Menus\MenusFactory;
use App\Pages\Implementation\Repositories\EloquentPagesRepository;
use App\Pages\Implementation\Repositories\EloquentPageVariantsRepository;
use App\Pages\Implementation\Services\PageVariants\Searcher\EloquentPageVariantsSearcher;
use App\Pages\PagesFacade;
use App\Pages\PagesFactory;
use App\Routes\Implementation\Repositories\EloquentRoutesRepository;
use App\Routes\RoutesFacade;
use App\Routes\RoutesFactory;
use App\SearchEngine\SearchEngineFacade;
use App\SearchEngine\SearchEngineFactory;
use App\Tags\Implementation\Repositories\EloquentTagsRepository;
use App\Tags\Implementation\Services\Searcher\EloquentTagsSearcher;
use App\Tags\TagsFacade;
use App\Tags\TagsFactory;
use Cviebrock\LaravelElasticsearch\Manager as ElasticsearchManager;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactoryContract;
use Illuminate\Events\Dispatcher as EventsDispatcher;
use Illuminate\Support\ServiceProvider;

final class FacadeServiceProvider extends ServiceProvider
{

    private const FACADES = [
        AnalyticsFacade::class,
        AttachmentsFacade::class,
        LanguagesFacade::class,
        MenusFacade::class,
        PagesFacade::class,
        RoutesFacade::class,
        SearchEngineFacade::class,
        TagsFacade::class,
    ];

    /**
     * @return void
     */
    public function register(): void
    {
        // Unit tests instantiate facades on their own, so we might as well just
        // skip this step
        if ($this->app->runningUnitTests()) {
            return;
        }

        // == Analytics == //
        $this->app->singleton(AnalyticsFacade::class, function (): AnalyticsFacade {
            return AnalyticsFactory::build(
                $this->app->make(EloquentEventsRepository::class),
                $this->app->make(EloquentRequestEventsSearcher::class)
            );
        });

        // == Attachments == //
        $this->app->singleton(AttachmentsFacade::class, function (): AttachmentsFacade {
            return AttachmentsFactory::build(
                $this->app->make(FilesystemFactoryContract::class)->disk('attachments'),
                $this->app->make(EloquentAttachmentsRepository::class)
            );
        });

        // == Languages == //
        $this->app->singleton(LanguagesFacade::class, function (): LanguagesFacade {
            return LanguagesFactory::build(
                $this->app->make(EloquentLanguagesRepository::class)
            );
        });

        // == Menus == //
        $this->app->singleton(MenusFacade::class, function (): MenusFacade {
            return MenusFactory::build(
                $this->app->make(EloquentMenuItemsRepository::class)
            );
        });

        // == Pages == //
        $this->app->singleton(PagesFacade::class, function (): PagesFacade {
            return PagesFactory::build(
                $this->app->make(EventsDispatcher::class),
                $this->app->make(EloquentPagesRepository::class),
                $this->app->make(EloquentPageVariantsRepository::class),
                $this->app->make(EloquentPageVariantsSearcher::class),
                $this->app->make(AttachmentsFacade::class),
                $this->app->make(TagsFacade::class)
            );
        });

        // == Routes == //
        $this->app->singleton(RoutesFacade::class, function (): RoutesFacade {
            return RoutesFactory::build(
                $this->app->make(EloquentRoutesRepository::class)
            );
        });

        // == Search engine == //
        $this->app->singleton(SearchEngineFacade::class, function (): SearchEngineFacade {
            return SearchEngineFactory::build(
                $this->app->make(ElasticsearchManager::class)->connection(),
                $this->app->make(PagesFacade::class)
            );
        });

        // == Tags == //
        $this->app->singleton(TagsFacade::class, function (): TagsFacade {
            return TagsFactory::build(
                $this->app->make(EventsDispatcher::class),
                $this->app->make(EloquentTagsRepository::class),
                $this->app->make(EloquentTagsSearcher::class)
            );
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        // Unit tests instantiate facades on their own, so we might as well just
        // skip this step
        if ($this->app->runningUnitTests()) {
            return;
        }

        foreach (self::FACADES as $facadeClass) {
            $facade = $this->app->make($facadeClass);

            if (method_exists($facade, 'boot')) {
                $facade->boot();
            }
        }
    }

}
