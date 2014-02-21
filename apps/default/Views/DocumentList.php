<?php
/* DocumentList View PHP */

class Views_DocumentList {
	public function __construct($documents) {
		$this->docs = $documents;
		?>
		<ul>
		
			<!-- loop documents -->
			<?php
			foreach($this->docs as $doc):
			$img = (strlen($doc->coverUrl)>0) ? $doc->getCoverUrl('thumb') : null;
			new Views_ListItem($type='doc', $id=$doc->id, $label=$doc->title, $doc->pubstate, $img);
			endforeach;
			?>
			<!-- end loop documents -->
		
		</ul>
		<?php
	}
}
?>