<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\Session;

class ToastService
{
    static protected string $TOAST_SESSION_NAME = 'toast_session';

    public function __construct(
        protected Session $session
    ) {
        //
    }

    public function all(): array
    {
        if ($this->session->has($this::$TOAST_SESSION_NAME)) {
            $toasts = $this->session->get($this::$TOAST_SESSION_NAME);
            $this->session->remove($this::$TOAST_SESSION_NAME);
        }

        return $toasts ?? [];
    }

    public function create(string $type, string $message): void
    {
        $this->add([
            'type' => $type,
            'content' => $message,
        ]);
    }

    protected function add(array $toastAttributes): void
    {
        $toasts = $this->session->get($this::$TOAST_SESSION_NAME);

        $toasts[] = $toastAttributes;
        $this->session->set($this::$TOAST_SESSION_NAME, $toasts);
    }

    protected function getOrCreate(): array
    {
        if ($this->session->has($this::$TOAST_SESSION_NAME)) {
            return $this->session->get($this::$TOAST_SESSION_NAME);
        }

        return [];
    }
}
