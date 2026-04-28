<?php

declare(strict_types=1);

namespace Motoadapt\Model;

/**
 * Performance trend over a session.
 */
enum Trend
{
    case PROGRESSION;
    case STABLE;
    case REGRESSION;
}
