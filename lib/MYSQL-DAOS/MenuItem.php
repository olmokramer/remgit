<?php
/* MenuItem DAO PHP */
namespace DAOS;

class MenuItem {
	public function findAll() {
		$result = $this->selectAll();
		return $result;
	}
	
	private function selectAll() {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT id, label FROM menuItems ORDER BY label ASC");
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
}
?>