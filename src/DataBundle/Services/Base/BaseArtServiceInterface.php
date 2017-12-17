<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 18:03
 */

namespace DataBundle\Services\Base;

use DataBundle\Entity\ArtEntityInterface;

interface BaseArtServiceInterface
{
    /**
     * Gets the specified ArtEntityInterface, either by slug or id
     *
     * @param string|int $idOrSlug
     * @return ArtEntityInterface
     */
    public function get($idOrSlug);

    /**
     * Gets the specified ArtEntityInterface by id
     *
     * @param int $id
     * @return ArtEntityInterface
     */
    public function getById(int $id);

    /**
     * Gets the specified ArtEntityInterface by slug
     *
     * @param string $slug
     * @return ArtEntityInterface,
     */
    public function getBySlug(string $slug);

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return ArtEntityInterface[]
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = ''): array;

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given ArtEntityInterface
     *
     * @param ArtEntityInterface $ArtEntityInterface
     * @return ArtEntityInterface
     */
    function save($ArtEntityInterface);

    /**
     * Deletes the given ArtEntityInterface
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id);

    /**
     * Updates the given field
     *
     * @param string $key
     * @param string $value
     * @param int $id
     * @return void
     */
    public function updateField(string $key, string $value, int $id);
}