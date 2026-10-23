<?php

namespace App\Support;

/**
 * Flags contact form submissions that look like bot spam, such as:
 *
 *   Name:    TRaPfxzYmyYnqYlajl
 *   Email:   j.le.wi.s4.2@gmail.com
 *   Message: ePMhllnugtxYbVBCNMabkh
 */
class ContactSpamDetector
{
    /**
     * Returns the reasons the submission looks like spam (empty when it looks fine).
     *
     * @return list<string>
     */
    public function reasons(?string $name, string $email, string $message): array
    {
        $reasons = [];

        if ($this->isRandomName((string) $name)) {
            $reasons[] = 'random name';
        }

        if ($this->isRandomMessage($message)) {
            $reasons[] = 'random message';
        }

        if ($this->isDottedGmail($email)) {
            $reasons[] = 'dotted gmail address';
        }

        return $reasons;
    }

    public function isSpam(?string $name, string $email, string $message): bool
    {
        $reasons = $this->reasons($name, $email, $message);

        // A message that is one long random string is never a real message.
        // The other signals alone could hit a real person, so require two of them.
        return in_array('random message', $reasons, true) || count($reasons) >= 2;
    }

    /**
     * One long word of letters with uppercase scattered through it, e.g. "TRaPfxzYmyYnqYlajl".
     */
    private function isRandomName(string $name): bool
    {
        $name = trim($name);

        if (! preg_match('/^[A-Za-z]{10,}$/', $name)) {
            return false;
        }

        return preg_match_all('/[A-Z]/', substr($name, 1)) >= 3;
    }

    /**
     * A message with no spaces that is just a long run of letters, e.g. "ePMhllnugtxYbVBCNMabkh".
     */
    private function isRandomMessage(string $message): bool
    {
        return (bool) preg_match('/^[A-Za-z]{12,}$/', trim($message));
    }

    /**
     * Gmail ignores dots, so bots pad addresses with them, e.g. "j.le.wi.s4.2@gmail.com".
     */
    private function isDottedGmail(string $email): bool
    {
        [$local, $domain] = array_pad(explode('@', strtolower(trim($email)), 2), 2, '');

        if (! in_array($domain, ['gmail.com', 'googlemail.com'], true)) {
            return false;
        }

        return substr_count($local, '.') >= 3;
    }
}
