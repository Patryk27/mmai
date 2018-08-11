<?php

namespace App\App\ViewComposers\Backend\Pages\Pages\CreateEdit;

use App\Languages\LanguagesFacade;
use App\Languages\Queries\GetAllLanguagesQuery;
use App\Tags\Queries\GetAllTagsQuery;
use App\Tags\TagsFacade;
use Illuminate\Contracts\View\View as ViewContract;

class FormComposer
{

    /**
     * @var LanguagesFacade
     */
    private $languagesFacade;

    /**
     * @var TagsFacade
     */
    private $tagsFacade;

    /**
     * @param LanguagesFacade $languagesFacade
     * @param TagsFacade $tagsFacade
     */
    public function __construct(
        LanguagesFacade $languagesFacade,
        TagsFacade $tagsFacade
    ) {
        $this->languagesFacade = $languagesFacade;
        $this->tagsFacade = $tagsFacade;
    }

    /**
     * @param ViewContract $view
     * @return void
     */
    public function compose(ViewContract $view): void
    {
        $view->with([
            'languages' => $this->languagesFacade->queryMany(
                new GetAllLanguagesQuery()
            ),

            'tags' => $this->tagsFacade->queryMany(
                new GetAllTagsQuery()
            )
        ]);
    }

}