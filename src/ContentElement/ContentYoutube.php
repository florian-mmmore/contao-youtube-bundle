<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\ContentElement;

use Contao\ContentYouTube as ContaoYoutube;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\PageModel;

class ContentYoutube extends ContaoYoutube
{
    protected $jsTemplate = 'js_youtube';

    protected function compile(): void
    {
        parent::compile();

        // Use youtube nocookie url for iframe source
        if (false !== strpos($this->Template->src, 'www.youtube.com')) {
            $this->Template->src = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $this->Template->src);
        }

        /** @var PageModel $page */
        $page = $GLOBALS['objPage'];

        // Add a splash image
        $this->setSplashImage();

        // Initialize the Template property
        $this->Template->schemaOrg = [];

        // Get the current root page
        $rootPage = PageModel::findById($page->rootId);
        if (null === $rootPage) {
            return;
        }

        // Collect schema.org data
        $schemaOrg = [
            'name' => '',
            'description' => '',
            'caption' => $this->caption,
            'thumbnailUrl' => $rootPage->getAbsoluteUrl() . '/' . $this->Template->splashImage->src,
            'uploadDate' => '',
            'duration' => '',
            'contentUrl' => $this->Template->src,
        ];
        $this->Template->schemaOrg = $schemaOrg;

        // find the data-protection page and generate the URL to it
        $dataProtectionPage = PageModel::findById((int) $rootPage->dataProtectionPage);
        if (null === $dataProtectionPage) {
            return;
        }

        $dataProtectionUrl = $dataProtectionPage->getFrontendUrl();

        $this->Template->dataProtectionUrl = $dataProtectionUrl;

        // Add the javascript to the page
        $jsTemplate = new FrontendTemplate($this->jsTemplate);
        $GLOBALS['TL_BODY']['youtube'] = $jsTemplate->parse();
    }

    /**
     * Set the youtube preview image as splash image.
     */
    protected function setSplashImage(): void
    {
        // Add the youtube splash image
        $file = FilesModel::findByUuid($this->singleSRC);

        if (null !== $file && is_file(TL_ROOT . '/' . $file->path)) {
            $this->singleSRC = $file->path;

            // reset size entry for youtube preview image
            if (!$this->splashImage) {
                $this->arrData['size'] = null;
            }

            $splashTemplate = new \stdClass();
            self::addImageToTemplate($splashTemplate, $this->arrData, null, null, $file);
            $this->Template->splashImage = $splashTemplate;
        }
    }
}
