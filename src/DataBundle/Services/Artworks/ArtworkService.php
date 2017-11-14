<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace DataBundle\Services\Artworks;

use DataBundle\Entity\Artwork;
use DataBundle\Services\Base\BaseArtService;
use Doctrine\ORM\EntityManager;
use HelperBundle\Services\Media\MediaServiceInterface;
use HelperBundle\Services\Slug\SlugServiceInterface;

class ArtworkService extends BaseArtService implements ArtworkServiceInterface
{
    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * ArtworkService constructor.
     * @param EntityManager $entityManager
     * @param MediaServiceInterface $mediaService
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManager $entityManager, MediaServiceInterface $mediaService, SlugServiceInterface $slugService)
    {
        parent::__construct($entityManager, $slugService, Artwork::class);
        $this->mediaService = $mediaService;
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id): void
    {
        $artwork = $this->get($id);
        if ($artwork->getPicture()) {
            $this->mediaService->deleteMedia($artwork->getPicture());
        }

        parent::delete($id);
    }

    public function get($idOrSlug): ?Artwork
    {
        return parent::get($idOrSlug);
    }

    /**
     * @inheritdoc
     */
    public function saveOrUpdate(Artwork $artwork): ?Artwork
    {
        $background = $artwork->getPictureResource();
        if ($background !== null) {
            $artwork->setPicture($this->mediaService->saveMedia($background, MediaServiceInterface::CONTENT_IMAGE));
        }

        return parent::save($artwork);
    }

    /**
     * @inheritdoc
     */
    public function getBySlug(string $slug): ?Artwork
    {
        return parent::getBySlug($slug);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): ?Artwork
    {
        return parent::getById($id);
    }
}