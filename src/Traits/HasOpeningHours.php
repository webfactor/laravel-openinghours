<?php

namespace Webfactor\Laravel\OpeningHours\Traits;

use Webfactor\Laravel\OpeningHours\Entities\OpeningHours;

/** @property OpeningHours opening_hours */
trait HasOpeningHours
{
    use OpeningHoursRelations;
    use OpeningHoursAttribute;
    use OpeningHoursScopes;
}
