<?php

namespace App\Models\Enums;

enum ChatStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
}
