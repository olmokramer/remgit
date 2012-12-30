<?php
/* MainMenu Controller PHP */
namespace Controllers;

//include files
include(DAOS_ROOT.'MenuItem.php');
include(DAOS_ROOT.'Category.php');
include(MODELS_ROOT.'MenuItem.php');

include(VIEWS_ROOT.'MainMenu.php');
include(VIEWS_ROOT.'MediaMenu.php');
require_once(VIEWS_ROOT.'ListItem.php');

class MainMenu {
	public function show() {
		$menuItemDAO = new \DAOS\MenuItem;
		$categoryDAO = new \DAOS\Category;
		$menuItems = $menuItemDAO->findAll();
		$categories = $categoryDAO->findAll();
		new \Views\MainMenu($menuItems, $categories);
	}
	
	public function showMediaMenu() {
		new \Views\MediaMenu();
	}
}
?>