<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\EventListener;

use Contao\ContentModel;
use Contao\DataContainer;
use Dreibein\YoutubeBundle\Youtube\PreviewImage;

class YoutubeElementListener
{
    /**
     * @var PreviewImage
     */
    private $previewImage;

    public function __construct(PreviewImage $previewImage)
    {
        $this->previewImage = $previewImage;
    }

    /**
     * Submit callback for youtube elements.
     *
     * @param DataContainer $dataContainer
     */
    public function getPreviewImage(DataContainer $dataContainer): void
    {
        if ('youtube' !== $dataContainer->activeRecord->type) {
            // the following code is only related to youtube elements
            return;
        }

        // Do not load an image from youtube if a custom image is used
        if ($dataContainer->activeRecord->splashImage) {
            return;
        }

        // if no youtube id is given, stop here
        if (!$dataContainer->activeRecord->youtube) {
            return;
        }

        // load the model of the content element
        $model = ContentModel::findById($dataContainer->activeRecord->id);
        if (!$model) {
            // model could not be loaded
            return;
        }

        // Get the image from Youtube
        $image = $this->previewImage->getImageById($dataContainer->activeRecord->youtube);

        // image could not be generated
        if (null === $image) {
            // remove old image
            $model->singleSRC = null;
            $model->save();

            return;
        }

        $model->singleSRC = $image->uuid;
        $model->save();
    }
}
