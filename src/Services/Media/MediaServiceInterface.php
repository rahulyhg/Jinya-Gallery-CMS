<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:29
 */

namespace Jinya\Services\Media;


interface MediaServiceInterface
{
    public const GALLERY_BACKGROUND = 'gallery_background';
    public const CONTENT_IMAGE = 'content_image';
    public const PROFILE_PICTURE = 'profile_picture';
    public const MENU_LOGO = 'menu_logo';

    /**
     * Saves the media to the storage and return the http url
     *
     * @param resource $file
     * @param string $type
     * @return string
     */
    public function saveMedia($file, string $type): string;

    /**
     * Deletes the media saved under the given url
     *
     * @param string $url
     * @return void
     */
    public function deleteMedia(string $url);
}