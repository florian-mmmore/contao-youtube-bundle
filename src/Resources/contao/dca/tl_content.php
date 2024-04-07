<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\Resources\contao\dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Dreibein\YoutubeBundle\EventListener\YoutubeElementListener;

$table = 'tl_content';

if (!\is_array($GLOBALS['TL_DCA'][$table]['config']['onsubmit_callback'])) {
    $GLOBALS['TL_DCA'][$table]['config']['onsubmit_callback'] = [];
}

// Create a submit callback so that the preview image can be loaded from youtube
$GLOBALS['TL_DCA'][$table]['config']['onsubmit_callback'][] = [YoutubeElementListener::class, 'getPreviewImage'];

// add splash image for contao version beneath 4.9
if (version_compare(VERSION, '4.9', '<')) {
    // Add an entry to the palettes
    PaletteManipulator::create()
        ->addLegend('splash_legend', 'player_legend', PaletteManipulator::POSITION_AFTER)
        ->addField('splashImage', 'splash_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette('youtube', $table)
    ;

    // Add the field to the selectors
    $GLOBALS['TL_DCA'][$table]['palettes']['__selector__'][] = 'splashImage';

    // Add an entry to the subpalettes
    $GLOBALS['TL_DCA'][$table]['subpalettes']['splashImage'] = 'singleSRC,size';

    // Add the preview-image-fields
    $GLOBALS['TL_DCA'][$table]['fields']['splashImage'] = [
        'label' => &$GLOBALS['TL_LANG'][$table]['splashImage'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true],
        'sql' => ['type' => 'boolean', 'notnull' => true, 'default' => 0],
    ];
}
