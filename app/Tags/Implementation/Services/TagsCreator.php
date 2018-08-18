<?php

namespace App\Tags\Implementation\Services;

use App\Tags\Exceptions\TagException;
use App\Tags\Implementation\Repositories\TagsRepositoryInterface;
use App\Tags\Models\Tag;

/**
 * @see \Tests\Unit\Tags\CreateTest
 */
class TagsCreator
{

    /**
     * @var TagsRepositoryInterface
     */
    private $tagsRepository;

    /**
     * @var TagsValidator
     */
    private $tagsValidator;

    /**
     * @param TagsRepositoryInterface $tagsRepository
     * @param TagsValidator $tagsValidator
     */
    public function __construct(
        TagsRepositoryInterface $tagsRepository,
        TagsValidator $tagsValidator
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->tagsValidator = $tagsValidator;
    }

    /**
     * @param array $tagData
     * @return Tag
     *
     * @throws TagException
     */
    public function create(array $tagData): Tag
    {
        $tag = new Tag(
            array_only($tagData, ['language_id', 'name'])
        );

        $this->tagsValidator->validate($tag);
        $this->tagsRepository->persist($tag);

        return $tag;
    }

}