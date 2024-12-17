<?php

namespace App\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Transactional {}
