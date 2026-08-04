<?php

declare(strict_types=1);

namespace BitskiWPCF7Booking\Tests\unit\integration\helpers;

use BitskiWPCF7Booking\integration\CF7Adapter;

class TestableCF7Adapter extends CF7Adapter
{
    public function sanitizePayloadForTest($payload) {
        return $this->sanitizePayload($payload);
    }
}
