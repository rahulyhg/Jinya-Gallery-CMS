<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 17:52
 */

namespace Jinya\Controller\Api\Menu;

use Jinya\Entity\Menu;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuLogoController extends BaseApiController
{
    /**
     * @Route("/api/menu/{id}/logo", methods={"GET"}, name="api_menu_logo_get")
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function getAction(int $id, MenuServiceInterface $menuService, MediaServiceInterface $mediaService): Response
    {
        /** @var $data Menu|array */
        list($data, $status) = $this->tryExecute(function () use ($id, $menuService) {
            return $menuService->get($id);
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        } else {
            return $this->file($mediaService->getMedia($data->getLogo()), $data->getName() . '.jpg');
        }
    }

    /**
     * @Route("/api/menu/{id}/logo", methods={"PUT"}, name="api_menu_logo_post")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param Request $request
     * @param MenuServiceInterface $menuService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function postAction(int $id, Request $request, MenuServiceInterface $menuService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $request, $menuService, $mediaService) {
            $path = $mediaService->saveMedia($request->getContent(true), MediaServiceInterface::MENU_LOGO);
            $menu = $menuService->get($id);
            $menu->setLogo($path);

            $menuService->save($menu);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}/logo", methods={"DELETE"}, name="api_menu_logo_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(int $id, MenuServiceInterface $menuService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $menuService, $mediaService) {
            $menu = $menuService->get($id);

            $mediaService->deleteMedia($menu->getLogo());

            $menu->setLogo('');
            $menuService->save($menu);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
