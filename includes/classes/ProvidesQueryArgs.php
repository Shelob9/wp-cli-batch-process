<?php
namespace PluginNamespace;


interface ProvidesQueryArgs {
    public function getPage(): int;
    public function setPage(int $page): int;
    public function getArgs(): array;
}