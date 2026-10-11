<?php

namespace App\Exceptions;

use Exception;

/** A document we were asked to read could not be turned into text. The message is shown to people. */
class DocumentTextException extends Exception {}
