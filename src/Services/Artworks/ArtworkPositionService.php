<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:09
 */

namespace Jinya\Services\Artworks;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Entity\Galleries\ArtGallery;
use Jinya\Services\Base\ArrangementServiceTrait;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;

class ArtworkPositionService implements ArtworkPositionServiceInterface
{
    use ArrangementServiceTrait;

    /** @var ArtGalleryServiceInterface */
    private $galleryService;

    /** @var ArtworkServiceInterface */
    private $artworkService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ArtworkPositionService constructor.
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtworkServiceInterface $artworkService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ArtGalleryServiceInterface $galleryService, ArtworkServiceInterface $artworkService, EntityManagerInterface $entityManager)
    {
        $this->galleryService = $galleryService;
        $this->artworkService = $artworkService;
        $this->entityManager = $entityManager;
    }

    /**
     * Saves the artwork in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param int $position
     * @return int
     */
    public function savePosition(string $gallerySlug, string $artworkSlug, int $position): int
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $artwork = $this->artworkService->get($artworkSlug);

        $artworkPosition = new ArtworkPosition();
        $artworkPosition->setArtwork($artwork);
        $artworkPosition->setGallery($gallery);

        $this->rearrangeArtworks(-1, $position, $artworkPosition, $gallery);

        return $artworkPosition->getId();
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param ArtworkPosition $artworkPosition
     * @param ArtGallery $gallery
     */
    private function rearrangeArtworks(int $oldPosition, int $newPosition, ArtworkPosition $artworkPosition, ArtGallery $gallery): void
    {
        $positions = $gallery->getArtworks()->toArray();
        $positions = $this->rearrange($positions, $oldPosition, $newPosition, $artworkPosition);

        $gallery->setArtworks(new ArrayCollection($positions));
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function updatePosition(string $gallerySlug, int $artworkPositionId, int $oldPosition, int $newPosition)
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $artworks = $gallery->getArtworks();

        $artwork = $artworks->filter(function (ArtworkPosition $item) use ($oldPosition) {
            return $item->getPosition() === $oldPosition;
        })->first();

        $this->rearrangeArtworks($oldPosition, $newPosition, $artwork, $gallery);
    }

    /**
     * Deletes the given artwork position
     *
     * @param int $id
     */
    public function deletePosition(int $id)
    {
        $this->entityManager
            ->createQueryBuilder()
            ->delete(ArtworkPosition::class, 'e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * Gets the artwork position for the given id
     *
     * @param int $id
     * @return ArtworkPosition
     */
    public function getPosition(int $id): ArtworkPosition
    {
        return $this->entityManager->find(ArtworkPosition::class, $id);
    }

    /**
     * Sets the artwork of the given artwork position to the new slug
     *
     * @param int $id
     * @param string $artworkSlug
     */
    public function updateArtwork(int $id, string $artworkSlug)
    {
        $artwork = $this->artworkService->get($artworkSlug);
        $this->entityManager
            ->createQueryBuilder()
            ->update(ArtworkPosition::class, 'e')
            ->set('e.artwork', $artwork->getId())
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}
