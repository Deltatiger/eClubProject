<?php

/**
 * This class is mainly used for loading the required pages to the site.
 * This avoids unwanted trouble with the mannual inclusion of files.
 *
 * @author DeltaTiger
 */
class Template {
    //This holds the name of the page we want to load.
    private $pageName;
    private $pageTitle;
    private $templateVars;
	private $rootPath;
    //This is the constructor of template class. Nothing much to do.
    function __construct() {
		$this->rootPath = $_SERVER['DOCUMENT_ROOT'].'/eclub/game/eClubProject/';
        $this->pageName = '';
    }
    //This function is used to set the page name.
    public function setPage($page)  {
        if (trim($page) != '')  {
            $this->pageName = $page;
            return true;
        }
        return false;
    }
    //This function is used to set the variables used in the template files.
    public function setTemplateVars($varsArray) {
        if (is_array($varsArray))   {
            foreach ($varsArray as $varName => $varValue)   {
                $this->templateVars[$varName] = $varValue;
            }
        }
    }
    //Used for setting a single variable of So
    public function setTemplateVar($varName, $varValue) {
        $this->templateVars[$varName] = $varValue;
    }
    
    //This function is used to check if a template variable is set and print it.
    public function printVar($varName)  {
        if ( isset($this->templateVars[$varName]) )     {
            echo $this->templateVars[$varName];
        }
    }
    
    public function getVar($varName)    {
        if ( isset($this->templateVars[$varName]) )     {
            return $this->templateVars[$varName];
        }
    }
    
    //This function is used to load the whole page where required.
    public function loadPage()  {
        //We have to insert the following pages.
        /*
         * 1. Header
		 *	-> Admin Ajax / User Ajax
         * 2. Navigation Bar
		 *	-> Admin navBar / User navBar
         * 3. Main Body
         * 4. Footer
         */
		/*//Deciding which Ajax to use.
		if(stripos($this->pageName, 'admin') == false)	{
			$this->setTemplateVar('isadmin', 0);
		} else {
			$this->setTemplateVar('isadmin', 1);
		}
		
        include $this->rootPath.'templates/header.php';
		//Deciding which navigation Bar to use.
        if(stripos($this->pageName, 'admin') == false)	{
			include $this->rootPath.'templates/user_navbar.php';
		} else {
			include $this->rootPath.'templates/admin_navbar.php';
		}
		//The Actuall page
        include $this->rootPath.'templates/t_'.$this->pageName.'.php';
		//The footer.
        include $this->rootPath.'templates/footer.html';
		*/
		//First we decide whether the page is an admin page or not.
		if(stripos($this->pageName, 'admin') === false)	{
			$navBarType = 'user';
			$this->setTemplateVar('isadmin', 0);
		} else {
			$navBarType = 'admin';
			$this->setTemplateVar('isadmin', 1);
		}
		
		//Now we also have to note wheather the user is an admin user or not.
		global $session;
		$this->setTemplateVar('isAdminUser', ($session->isAdminUser() ? 1 : 0));
		
		//Now we load all the pages.
		include $this->rootPath.'templates/header.php';
		include $this->rootPath.'templates/'.$navBarType.'_navbar.php';
		include $this->rootPath.'templates/t_'.$this->pageName.'.php';
		include $this->rootPath.'templates/footer.html';
    }
    
    //This function is used to set the page title.
    public function setPageTitle($pageTitle)  {
        $this->pageTitle = $pageTitle;
    }
    
    //This function is used to get the page title for the template page.
    private function getPageTitle() {
        return $this->pageTitle;
    }
}

?>