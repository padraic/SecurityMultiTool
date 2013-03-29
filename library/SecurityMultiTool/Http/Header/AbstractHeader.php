<?php

namespace SecurityMultiTool\Http\Header;

use SecurityMultiTool\Common;
use SecurityMultiTool\Exception;
use SecurityMultiTool\Http\HttpsDetector;

abstract class AbstractHeader extends Common\AbstractOptions
{

    protected function isHttpsRequest()
    {
        return HttpsDetector::isHttpsRequest();
    }

}