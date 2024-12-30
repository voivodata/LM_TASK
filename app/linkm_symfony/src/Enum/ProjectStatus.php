<?php

namespace App\Enum;

enum ProjectStatus: string
{
    case IN_PROGRESS = 'inprogress';
    case FINISHED = 'finished';
}
