<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 20:57
 */

namespace Jinya\Controller\Api\Gallery;


use Jinya\Entity\Gallery;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GalleryBackgroundController extends BaseApiController
{
    /**
     * @Route("/api/gallery/{slug}/background", methods={"GET"}, name="api_gallery_background_get")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param string $slug
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function getBackgroundImageAction(string $slug, Request $request, GalleryServiceInterface $galleryService): Response
    {
        /** @var $data Gallery */
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $slug) {
            $gallery = $galleryService->get($slug);
            if (empty($gallery->getBackground())) {
                throw new FileNotFoundException($gallery->getName());
            }

            return $gallery->getBackground();
        });

        if ($status !== 200) {
            return $this->json($data, $status);
        } else {
            return $this->redirect($data);
        }
    }

    /**
     * @Route("/api/gallery/{slug}/background", methods={"PUT"}, name="api_gallery_background_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function putBackgroundImageAction(string $slug, Request $request, GalleryServiceInterface $galleryService, MediaServiceInterface $mediaService, UrlGeneratorInterface $urlGenerator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $mediaService, $urlGenerator, $slug) {
            $gallery = $galleryService->get($slug);

            $background = $request->getContent(true);
            $backgroundPath = $mediaService->saveMedia($background, MediaServiceInterface::GALLERY_BACKGROUND);

            if ($background) {
                $gallery->setBackground($backgroundPath);
            }

            $galleryService->saveOrUpdate($gallery);

            return $urlGenerator->generate('api_gallery_background_get', ['slug' => $gallery->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}/background", methods={"DELETE"}, name="api_gallery_background_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteBackgroundImageAction(string $slug, Request $request, GalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $mediaService, $slug) {
            $gallery = $galleryService->get($slug);
            $mediaService->deleteMedia($gallery->getBackground());
            $gallery->setBackground(null);

            $galleryService->saveOrUpdate($gallery);

            return null;
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}