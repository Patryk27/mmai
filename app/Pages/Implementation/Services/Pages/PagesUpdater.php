<?php

namespace App\Pages\Implementation\Services\Pages;

use App\Attachments\AttachmentsFacade;
use App\Attachments\Exceptions\AttachmentException;
use App\Attachments\Queries\GetAttachmentByIdQuery;
use App\Pages\Events\PageUpdated;
use App\Pages\Exceptions\PageException;
use App\Pages\Implementation\Repositories\PagesRepository;
use App\Pages\Implementation\Services\PageVariants\PageVariantsCreator;
use App\Pages\Implementation\Services\PageVariants\PageVariantsUpdater;
use App\Pages\Models\Page;
use App\Pages\Models\PageVariant;
use App\Tags\Exceptions\TagException;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcherContract;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @see \Tests\Unit\Pages\UpdateTest
 */
class PagesUpdater
{

    /**
     * @var EventsDispatcherContract
     */
    private $eventsDispatcher;

    /**
     * @var PagesRepository
     */
    private $pagesRepository;

    /**
     * @var PageVariantsCreator
     */
    private $pageVariantsCreator;

    /**
     * @var PageVariantsUpdater
     */
    private $pageVariantsUpdater;

    /**
     * @var PagesValidator
     */
    private $pagesValidator;

    /**
     * @var AttachmentsFacade
     */
    private $attachmentsFacade;

    /**
     * @param EventsDispatcherContract $eventsDispatcher
     * @param PagesRepository $pagesRepository
     * @param PageVariantsCreator $pageVariantsCreator
     * @param PageVariantsUpdater $pageVariantsUpdater
     * @param PagesValidator $pagesValidator
     * @param AttachmentsFacade $attachmentsFacade
     */
    public function __construct(
        EventsDispatcherContract $eventsDispatcher,
        PagesRepository $pagesRepository,
        PageVariantsCreator $pageVariantsCreator,
        PageVariantsUpdater $pageVariantsUpdater,
        PagesValidator $pagesValidator,
        AttachmentsFacade $attachmentsFacade
    ) {
        $this->eventsDispatcher = $eventsDispatcher;
        $this->pagesRepository = $pagesRepository;
        $this->pageVariantsCreator = $pageVariantsCreator;
        $this->pageVariantsUpdater = $pageVariantsUpdater;
        $this->pagesValidator = $pagesValidator;
        $this->attachmentsFacade = $attachmentsFacade;
    }

    /**
     * @param Page $page
     * @param array $pageData
     * @return void
     *
     * @throws AttachmentException
     * @throws PageException
     * @throws TagException
     */
    public function update(Page $page, array $pageData): void
    {
        $page->fill([
            'notes' => array_get($pageData, 'page.notes'),
        ]);

        foreach (array_get($pageData, 'pageVariants', []) as $pageVariantData) {
            $this->savePageVariant($page, $pageVariantData);
        }

        $this->saveAttachments($page, array_get($pageData, 'attachment_ids') ?? []);
        $this->save($page);
    }

    /**
     * @param Page $page
     * @param array $pageVariantData
     * @return void
     *
     * @throws PageException
     * @throws TagException
     */
    private function savePageVariant(Page $page, array $pageVariantData): void
    {
        if (array_has($pageVariantData, 'id')) {
            /**
             * @var PageVariant|null $pageVariant
             */
            $pageVariant = $page->pageVariants->firstWhere('id', $pageVariantData['id']);

            if (is_null($pageVariant)) {
                throw new PageException(
                    sprintf('Page variant [id=%d] was not found inside page [id=%d].', $pageVariantData['id'], $page->id)
                );
            }

            $this->pageVariantsUpdater->update($pageVariant, $pageVariantData);
        } else {
            $page->pageVariants->push(
                $this->pageVariantsCreator->create($page, $pageVariantData)
            );
        }
    }

    /**
     * @param Page $page
     * @param int[] $attachmentIds
     * @return void
     *
     * @throws AttachmentException
     */
    private function saveAttachments(Page $page, array $attachmentIds): void
    {
        $page->setRelation('attachments', new EloquentCollection());

        foreach ($attachmentIds as $attachmentId) {
            $attachment = $this->attachmentsFacade->queryOne(
                new GetAttachmentByIdQuery($attachmentId)
            );

            $page->attachments->push($attachment);
        }
    }

    /**
     * @param Page $page
     * @return void
     *
     * @throws PageException
     */
    private function save(Page $page): void
    {
        $this->pagesValidator->validate($page);

        $this->pagesRepository->persist($page);

        $this->eventsDispatcher->dispatch(
            new PageUpdated($page)
        );
    }

}
