<?php
namespace WpCliBatchProcess;

interface ProvidesQueryArgs {
	public function getPerPage(): int;
	public function setPerPage( int $perPage ): int;
	public function getPage(): int;
	public function setPage( int $page): int;
	public function getArgs(): array;
}
