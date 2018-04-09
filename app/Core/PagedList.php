<?php

namespace App\Core;

class PagedList 
{
    
    public $viewList;
    public $totalItems;
    public $pageNumber;
    public $PageSize;
    public $totalPages;
    public $hasPreviousPage;
    public $hasNextPage;
    public $nextPageNumber;
    public $previousPageNumber;

	public function __construct($list, $pageNumber=1, $pageSize=999)
	{
            $paginator=$list->paginate($pageSize, ['*'], 'page', $pageNumber);

			$this->totalItems = $paginator->total();
			$this->pageNumber = $pageNumber;
            $this->pageSize = $pageSize;
            
            $this->viewList = $paginator->items();

		
			$this->totalPages = $paginator->lastPage();

			$this->hasPreviousPage = $this->pageNumber > 1;
			$this->hasNextPage = $paginator->hasMorePages();
			$this->nextPageNumber = $this->hasNextPage ? $this->pageNumber + 1 : $this->totalPages;
			$this->previousPageNumber = $this->hasPreviousPage ? $this->pageNumber - 1 : 1;
			


	}	

}
