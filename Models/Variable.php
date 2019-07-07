<?php

namespace Modules\Pages\Models;

use Modules\Pages\Models\db\Variable as DBModel;

class Variable extends DBModel
{
    /**
     * Route key.
     *
     * @return mixed|string
     */
    public function getRouteKey()
    {
        return 'key';
    }
}
