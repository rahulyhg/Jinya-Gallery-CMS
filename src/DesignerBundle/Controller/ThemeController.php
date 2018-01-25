<?php

namespace DesignerBundle\Controller;

use DataBundle\Services\Theme\ThemeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_replace_recursive;
use function preg_match_all;

class ThemeController extends Controller
{
    /**
     * @Route("/theme/", name="designer_theme_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var ThemeServiceInterface $themeService */
        $themeService = $this->get('jinya_gallery.services.theme_service');
        $themes = $themeService->getAllThemes();

        return $this->render('@Designer/theme/index.html.twig', [
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/theme/{name}", name="designer_theme_config")
     *
     * @param string $name
     * @param Request $request
     * @return Response
     */
    public function configAction(string $name, Request $request): Response
    {
        $themeService = $this->get('jinya_gallery.services.theme_service');

        if ($request->isMethod('POST')) {
            $oldConfiguration = $themeService->getTheme($name)->getConfiguration();
            $configuration = $request->get('configuration');
            $files = $request->files;
            $mediaService = $this->get('jinya_gallery.services_media.media_service');

            foreach ($files as $file) {
                list($result, $key, $uploadedFile) = $this->getKeyAndFile($file);
                if ($result) {
                    $path = $mediaService->saveMedia($uploadedFile, 'themeconfig-' . $name);

                    $temp = &$configuration;
                    preg_match_all('/\[(.*?)\]/', $key, $exploded);
                    foreach ($exploded[1] as $elem) {
                        $temp = &$temp[$elem];
                    }
                    $temp = $path;
                    unset($temp);

                    $configuration[$key] = $path;
                }
            }

            $themeService->saveConfig(array_replace_recursive($oldConfiguration, $configuration), $name);
        }

        $configForm = $themeService->getConfigForm($name);
        $config = $themeService->getTheme($name)->getConfiguration();

        return $this->render('@Designer/theme/config.html.twig', [
            'configForm' => $configForm,
            'config' => $config,
            'theme' => $themeService->getTheme($name)
        ]);
    }

    private function getKeyAndFile(array $data = null, string $key = ''): array
    {
        if ($data) {
            foreach ($data as $aKey => $elem) {
                $key .= "[$aKey]";

                if ($elem instanceof UploadedFile) {
                    return [true, $key, $elem];
                }

                return $this->getKeyAndFile($elem, $key);
            }
        }

        return [false, null, null];
    }
}
