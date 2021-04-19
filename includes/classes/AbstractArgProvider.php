<?php
namespace WpCliBatchProcess;

abstract class AbstractArgProvider implements ProvidesQueryArgs {

	protected $page;
	protected $perPage;

	/**
	 * Get current page
	 *
	 * @return integer
	 */
	public function getPage():int {
		return isset( $this->page ) ? $this->page : 1;
	}
	/**
	 * Set current page
	 *
	 * @param integer $page
	 * @return integer
	 */
	public function setPage( int $page ): int {
		$this->page = $page;
		return $this->page;
	}

	/**
	 * Set per page
	 *
	 * @return integer
	 */
	public function getPerPage(): int {
		return $this->perPage;
	}

	/**
	 * Set per page
	 *
	 * @param integer $perPage
	 * @return integer
	 */
	public function setPerPage( int $perPage ): int {
		$this->perPage = $perPage;
		return $this->perPage;
	}

	public function mergeArgs( $args = [] ): array {
		if ( ! empty( $args ) ) {
			return array_merge(
				$args,
				[
					'paged'          => $this->getPage(),
					'posts_per_page' => $this->getPerPage(),
				]
			);
		}
		return [
			'paged'          => $this->getPage(),
			'posts_per_page' => $this->getPerPage(),
		];
	}

}
