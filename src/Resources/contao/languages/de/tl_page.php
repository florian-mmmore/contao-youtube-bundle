<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

$table = 'tl_page';

if (!$GLOBALS['TL_LANG'][$table]['dataProtectionPage']) {
    $GLOBALS['TL_LANG'][$table]['dataProtectionPage'] = ['Datenschutzseite', 'Wählen Sie hier Ihre Datenschutzseite aus.'];
}
