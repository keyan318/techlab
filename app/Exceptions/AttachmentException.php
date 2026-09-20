<?php

namespace App\Exceptions;

use RuntimeException;

/** A chat attachment that can't be used; the message is safe to show the student. */
class AttachmentException extends RuntimeException {}
