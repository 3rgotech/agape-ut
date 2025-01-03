<?php

namespace App\Models\Contracts;

interface WithSubmission
{
    public function canBeSubmitted(): bool;
    public function getSubmissionErrors(): array;

    public function submit(bool $force = false): static;
    public function unsubmit(string $message): static;

    public function getSubmissionNotification(string $name): ?string;
    public function resolveCreator(): ?\App\Models\User;
    public function resolveAdmins(): \Illuminate\Support\Collection|array;
    public function resolveLabDirectors(): \Illuminate\Support\Collection|array;
    public function canBeUnsubmitted(): bool;
}
