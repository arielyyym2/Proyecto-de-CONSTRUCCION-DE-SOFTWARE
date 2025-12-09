<?php
interface IValidator
{
    public function validate(array $data): array;
    public function isValid(array $data): bool;
}
