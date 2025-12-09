<?php
abstract class BaseController
{
    protected function showSuccess(string $message): void
    {
        echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
    }

    protected function showError(string $message): void
    {
        echo '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
    }

    protected function showErrors(array $errors): void
    {
        echo '<div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul></div>';
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }
}
