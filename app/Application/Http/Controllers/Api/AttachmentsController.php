<?php

namespace App\Application\Http\Controllers\Api;

use App\Application\Http\Controllers\Controller;
use App\Application\Http\Requests\Backend\Attachments\CreateAttachmentRequest;
use App\Attachments\AttachmentsFacade;
use Throwable;

class AttachmentsController extends Controller {
    /** @var AttachmentsFacade */
    private $attachmentsFacade;

    public function __construct(AttachmentsFacade $attachmentsFacade) {
        $this->attachmentsFacade = $attachmentsFacade;
    }

    /**
     * @param CreateAttachmentRequest $request
     * @return array
     * @throws Throwable
     */
    public function store(CreateAttachmentRequest $request): array {
        $attachment = $this->attachmentsFacade->createFromFile(
            $request->file('attachment')
        );

        $attachmentPresenter = $attachment->getPresenter();

        return [
            'id' => $attachment->id,
            'name' => $attachment->name,
            'mime' => $attachment->mime,
            'size' => $attachment->getSizeForHumans(),
            'url' => $attachmentPresenter->getUrl(),
        ];
    }
}