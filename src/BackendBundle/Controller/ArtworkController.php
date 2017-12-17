<?php

namespace BackendBundle\Controller;

use BackendBundle\Form\ArtworkType;
use DataBundle\Entity\Artwork;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtworkController extends Controller
{
    /**
     * @Route("/artworks", name="backend_artworks_index")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->redirectToRoute('backend_artworks_overview');
    }

    /**
     * @Route("/artworks/overview", name="backend_artworks_overview")
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        return $this->render('@Backend/artworks/overview.html.twig', [
            'search' => $request->get('keyword', '')
        ]);
    }

    /**
     * @Route("/artworks/get", name="backend_artworks_getAll")
     *
     * @param Request $request
     * @return Response
     */
    public function getArtworks(Request $request): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $offset = $request->get('offset', 0);
        $count = $request->get('count', 12);
        $keyword = $request->get('keyword', '');
        $allData = $artworkService->getAll($offset, $count, $keyword);
        $allCount = $artworkService->countAll($keyword);

        return $this->json([
            'data' => $allData,
            'more' => $allCount > $count + $offset,
            'moreLink' => $this->generateUrl('backend_artworks_getAll', [
                'keyword' => $keyword,
                'count' => $count,
                'offset' => $offset + $count
            ])
        ]);
    }

    /**
     * @Route("/artworks/add", name="backend_artworks_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->createForm(ArtworkType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artworkService = $this->get('jinya_gallery.services.artwork_service');
            $artworkService->saveOrUpdate($data);
            return $this->redirectToRoute('backend_artworks_index');
        }

        return $this->render('@Backend/artworks/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/artworks/edit/{id}", name="backend_artworks_edit")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $artwork = $artworkService->get($id);
        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artworkService->saveOrUpdate($data);
            return $this->redirectToRoute('backend_artworks_details', [
                'id' => $data->getId()
            ]);
        }

        return $this->render('@Backend/artworks/edit.html.twig', [
            'form' => $form->createView(),
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/artworks/details/{id}", name="backend_artworks_details")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function detailsAction(Request $request, int $id): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $artwork = $artworkService->get($id);
        return $this->render('@Backend/artworks/details.html.twig', [
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/artworks/delete/{id}", name="backend_artworks_delete")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        if ($request->isMethod('POST')) {
            $artworkService->delete($id);
            return $this->redirectToRoute('backend_artworks_overview');
        }

        $artwork = $artworkService->get($id);
        return $this->render('@Backend/artworks/delete.html.twig', [
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/artworks/history/{id}", name="backend_artworks_history")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function historyAction(Request $request, int $id): Response
    {
        return $this->forward('BackendBundle:History:index', [
            'class' => Artwork::class,
            'id' => $id,
            'resetRoute' => 'backend_artworks_reset',
            'layout' => '@Backend/artworks/artworks_base.html.twig'
        ]);
    }

    /**
     * @Route("/artworks/history/{id}/reset", name="backend_artworks_reset", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function resetAction(Request $request, int $id): Response
    {
        $origin = $request->get('origin');
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $key = $request->get('key');
        $value = $request->get('value');

        $artworkService->updateField($key, $value, $id);

        return $this->redirect($origin);
    }
}