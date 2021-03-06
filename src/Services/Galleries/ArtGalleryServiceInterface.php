<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace Jinya\Services\Galleries;

use Jinya\Entity\Galleries\ArtGallery;
use Jinya\Entity\Label;
use Jinya\Services\Base\LabelEntityServiceInterface;

interface ArtGalleryServiceInterface extends LabelEntityServiceInterface
{
    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return ArtGallery
     */
    public function get(string $slug);

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return ArtGallery[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array;

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @param Label|null $label
     * @return int
     */
    public function countAll(string $keyword = '', Label $label = null): int;

    /**
     * Saves or updates the given gallery
     *
     * @param ArtGallery $gallery
     * @return ArtGallery
     */
    public function saveOrUpdate($gallery);

    /**
     * Deletes the given gallery
     *
     * @param ArtGallery $gallery
     */
    public function delete($gallery): void;

    /**
     * Updates the given field
     *
     * @param string $key
     * @param string $value
     * @param int $id
     */
    public function updateField(string $key, string $value, int $id);
}
