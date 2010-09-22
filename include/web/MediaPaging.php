<?php


class MediaPaging
{
	public $itemsAtOnePage = 10;
	public $pageLimit = 10;
	
	public $pageCount;
	public $items;
	public $previousPage;
	public $currentPage;
	public $nextPage;
	public $pages;
	
	function generate( $items, $page )
	{
		$this->pageCount = ceil( count($items) / $this->itemsAtOnePage );
		if( $this->pageCount > $this->pageLimit ) $this->pageCount = $this->pageLimit;
		
		if( !is_numeric($page) || $page < 1 ) $page = 1;
		if( $this->pageCount < $page ) $page = $this->pageCount;
		
		$this->currentPage = $page;
		if( $page < 2 ) $this->previousPage = null; else $this->previousPage = $page - 1;
		if( $page >= $this->pageCount ) $this->nextPage = null; else $this->nextPage = $page + 1;
		
		$this->items = array_slice( $items, ($this->currentPage-1) * $this->itemsAtOnePage, $this->itemsAtOnePage, true );
		
		for( $p = 1 ; $p <= $this->pageCount; $p++ ) $this->pages[] = $p;		
	}
}