<?php

namespace Nuhel\FilamentCropper\Values;

enum ViewMode: int
{
    case NO_RESTRICTIONS = 0;
    case RESTRICT_CROP_BOX = 1;
    case FIT_CANVAS = 2;
    case FIT_FILL_CANVAS = 3;
}
