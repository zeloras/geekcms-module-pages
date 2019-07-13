<?php

namespace GeekCms\Pages\Models;

use GeekCms\Pages\Models\db\Variable as DBModel;

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
