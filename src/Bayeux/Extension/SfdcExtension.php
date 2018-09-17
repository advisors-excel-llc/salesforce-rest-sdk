<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/11/18
 * Time: 1:58 PM
 */

namespace AE\SalesforceRestSdk\Bayeux\Extension;

use AE\SalesforceRestSdk\Bayeux\Message;
use Psr\Log\LoggerInterface;

class SfdcExtension implements ExtensionInterface
{
    public const NAME = 'sfdc';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function prepareSend(Message $message): void
    {
        // Intentionally left blank
        return;
    }

    public function processReceive(Message $message)
    {
        if (null !== $this->logger && !$message->isSuccessful()) {
            $ext = $message->getExt();

            if (null !== $ext
                && array_key_exists($this->getName(), $ext)
                && array_key_exists("failureReason", $ext[$this->getName()])) {
                $this->logger->error("{error}", [
                    'error' => $ext[$this->getName()]['failureReason']
                ]);
            }
        }
    }

}
