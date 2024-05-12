<?php

namespace core_ai;

interface LoggerAwareInterface {
    public function setLogger(LoggerInterface $logger);
}
