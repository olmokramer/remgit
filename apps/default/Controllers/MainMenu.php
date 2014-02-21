<?php
/* MainMenu Controller PHP */

//include backend files
include(DAOS_ROOT.'Media.php');

//include files
include(DAOS_ROOT.'MenuItem.php');
include(DAOS_ROOT.'Category.php');
include(MODELS_ROOT.'MenuItem.php');

include(VIEWS_ROOT.'MainMenu.php');
include(VIEWS_ROOT.'MediaMenu.php');
require_once(VIEWS_ROOT.'ListItem.php');

class Controllers_MainMenu {
	public function show() {
		$menuItemDAO = new DAOS_MenuItem;
		$categoryDAO = new DAOS_Category;
		$menuItems = $menuItemDAO->findAll();
		$categories = $categoryDAO->findAll();
		new Views_MainMenu($menuItems, $categories);
	}

	public function showMediaMenu() {
		$mediaDAO = new DAOS_Media;
		$batches = $mediaDAO->findDistinctBatches();
		new Views_MainMenu($batches);
	}
}
?>