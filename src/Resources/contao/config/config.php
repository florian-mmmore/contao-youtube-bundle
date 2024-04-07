<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\Resources\contao\config\config;

use Dreibein\YoutubeBundle\ContentElement\ContentYoutube;

$GLOBALS['TL_CTE']['media']['youtube'] = ContentYoutube::class;
