<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 20:57
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Entity\Galleries\VideoGallery;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoGalleryBackgroundController extends BaseApiController
{
    /**
     * @Route("/api/gallery/video/{slug}/background", methods={"GET"}, name="api_gallery_video_background_get")
     *
     * @param string $slug
     * @param Request $request
     * @param VideoGalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function getBackgroundImageAction(string $slug, Request $request, VideoGalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
    {
        /** @var $data VideoGallery|array */
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $slug) {
            $gallery = $galleryService->get($slug);
            if (empty($gallery->getBackground())) {
                throw new FileNotFoundException($gallery->getName());
            }

            return $gallery;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        } else {
            return $this->file($mediaService->getMedia($data->getBackground()), $data->getName() . '.jpg');
        }
    }

    /**
     * @Route("/api/gallery/video/{slug}/background", methods={"PUT"}, name="api_gallery_video_background_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param VideoGalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function putBackgroundImageAction(string $slug, Request $request, VideoGalleryServiceInterface $galleryService, MediaServiceInterface $mediaService, UrlGeneratorInterface $urlGenerator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $mediaService, $urlGenerator, $slug) {
            $gallery = $galleryService->get($slug);

            $background = $request->getContent(true);
            $backgroundPath = $mediaService->saveMedia($background, MediaServiceInterface::GALLERY_BACKGROUND);

            if ($background) {
                $gallery->setBackground($backgroundPath);
            }

            $galleryService->saveOrUpdate($gallery);

            return $urlGenerator->generate('api_gallery_video_background_get', ['slug' => $gallery->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/video/{slug}/background", methods={"DELETE"}, name="api_gallery_video_background_delete")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     * @param VideoGalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteBackgroundImageAction(string $slug, Request $request, VideoGalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $mediaService, $slug) {
            $gallery = $galleryService->get($slug);
            $mediaService->deleteMedia($gallery->getBackground());
            $gallery->setBackground(null);

            $galleryService->saveOrUpdate($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
