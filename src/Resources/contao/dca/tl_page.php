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

$table = 'tl_page';

// Check if the entry must be added.
if (!$GLOBALS['TL_DCA'][$table]['fields']['dataProtectionPage']) {
    // Add the data-protection-page-field to the palette
    $paletteManipulator = PaletteManipulator::create()
        ->addField('dataProtectionPage', 'global_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette('root', $table)
    ;

    if (version_compare(VERSION, '4.9', '>=')) {
        $paletteManipulator->applyToPalette('rootfallback', $table);
    }

    // Add the data-protection-page-field to the dca, so it can be added to the database
    $GLOBALS['TL_DCA'][$table]['fields']['dataProtectionPage'] = [
        'label' => &$GLOBALS['TL_LANG'][$table]['dataProtectionPage'],
        'exclude' => true,
        'inputType' => 'pageTree',
        'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr', 'multiple' => false],
        'sql' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'notnull' => true, 'default' => 0],
    ];
}
