<?php
/* Document View PHP */
namespace Views;

class Document {
	public function __construct($doc, $categories) {
		$this->doc = $doc;
		$this->categories = $categories;
		?>
		
		<!-- container -->
		<div id="document-container">
			
			<!-- tabs -->
			
			<div id="document-tabs">

				<!-- tabs nav -->

				<ul>
				
					<li>
					<a href="#tabs-fields">fields</a>
					</li>
				
					<?php
					if(count($this->doc->galleries)>0):
					foreach($this->doc->galleries as $gallery):
					?>
					<li>
					<a href="#tabs-<?=$gallery->label?>" data-kind="<?=$gallery->kind?>"><?=$gallery->label?></a>
					</li>
					<?php
					endforeach;
					endif
					?>
									
				</ul>
				
				<!-- end tabs nav -->

				<!-- tabs fields -->
				<div id="tabs-fields">
				
					<p><br><br></p>

					<form data-name="document-form" id="document-form">
					<div id="dochead">
					
						<div class="imageHolder" id="coverImage">
							<div class="image">
								<?php if(isset($this->doc->coverUrl)): ?>
								<img src="<?=THUMBS.$this->doc->coverUrl?>" data-cleanurl="<?=$this->doc->coverUrl?>">
								<?php else: ?>
								<div class="chooseImage">choose image</div>
								<?php endif; ?>
							</div>
						</div>

						<div id="imagePickerHolder">
							<div id="imagePicker">
								<div id="imagepicker-media">
								</div>
							</div>
						</div>
						
						<div id="doc-title-container">
							<div class="properties-head">title</div><br>
	
							<ul class="properties-list">
								<li>
								<input style="width:350px" data-kind="native" data-id="null" data-name="title" type="text" value="<?=htmlspecialchars($this->doc->title)?>" data-fieldtype="single" class="input-title">
								</li>
							</ul>
				
						</div>
						
					</div>

					<!-- categories -->
					
					<div class="properties-head">Categories</div><br>

					<ul class="properties-list">
						<?php
						if(count($this->categories)>0):
						foreach($this->categories as $id => $label):
						?>
						
						<li>
						<?php $checked = (in_array($label, $this->doc->categories)) ? "checked=checked" : ""; ?>
						<input type="checkbox" data-id="<?=$id?>" <?=$checked?>> <?=$label?>
						</li>
						
						<?php
						endforeach;
						else:
						echo '<li><i>No available categories</i></li>';
						endif;
						?>
					</ul>
						
					</p>
					
					<!-- end categories -->
		
					<!-- custom template fields -->
						
					<?php
					if(count($this->doc->customfields)>0):
					foreach($this->doc->customfields as $label => $field):
					?>
										
					<div class="properties-head"><?=$label?></div><br>

					<ul class="properties-list">
						
						<?php
						switch($field->fieldtype) :
						case 'single':
						?>
						
						<li>
						<input data-kind="custom" data-id="<?=$field->id?>" data-name="<?=$label?>" data-fieldtype="single" type="text" value="<?=htmlspecialchars($field->value)?>">
						</li>
						
						<?php
						break;
						case 'multi':
						?>
						
						<li>
						<textarea data-kind="custom" data-id="<?=$field->id?>" data-name="<?=$label?>" data-inputtype="<?=$field->inputtype?>" data-fieldtype="multi"><?=$field->value?></textarea>
						</li>
	
    					<?php
						break;
						endswitch;
						?>

					</ul>
					
					<?php
					endforeach;
					endif;
					?>
					
					<!-- end custom template fields -->

					<?php
					$publishDate = (strlen($this->doc->published)>0) ? date("Y-m-d H:i", $this->doc->published) : date("Y-m-d H:i");
					$year = substr($publishDate, 0, 4);
					$month = substr($publishDate, 5, 2);
					$day = substr($publishDate, 8, 2);
					$hour = substr($publishDate, 11, 2);
					$minute = substr($publishDate, 14, 2);
					?>
				
					</form>
				
					<!-- publish settings -->
					<form id="pubstate-form">
					
					<p>
					
					<div class="properties-head">Publish state</div><br>
	
					<ul class="properties-list">
						<li>
						
						<select name="publishState" id="publishState" data-name="pubstate">
						
						<?php
						$publishOptions = array( 0 =>'Not Published', 1 => 'Published');
						foreach($publishOptions as $key => $value): ?>
						
						<option <?php if($this->doc->pubstate == $key): echo 'selected=selected'; endif; ?> value="<?=$key?>">
						<?=$value?>
						</option>
						
						<?php endforeach; ?>
						</select>
						
						<div id="publishdate" <?php if($this->doc->pubstate==0): ?> style="display:none;"<?php endif; ?>
				>
							<input type="text" value="<?=$day?>" maxlength="2" style="width:25px;" data-name="pubdate-day">
							<input type="text" value="<?=$month?>" maxlength="2" style="width:25px;" data-name="pubdate-month">
							<input type="text" value="<?=$year?>" maxlength="4" style="width:40px;" data-name="pubdate-year">
		
							&nbsp;
							&nbsp;
		
							<input type="text" value="<?=$hour?>" maxlength="2" style="width:25px;" data-name="pubdate-hour"> :
							<input type="text" value="<?=$minute?>" maxlength="2" style="width:25px;" data-name="pubdate-minute">
		
						</div>
						</li>
					</ul>
			
					</form>	
					</p>
					<!-- end publish settings -->
					
				</div>
				<!-- end tabs fields -->
				
				<!-- galleries -->
				<?php
				if(count($this->doc->galleries)>0):
				foreach($this->doc->galleries as $gallery):
				?>
				
				<!-- gallery tab -->
				<div class="document-gallery" id="tabs-<?=$gallery->label?>" data-id="<?=$gallery->id?>">
			
					<div class="add-media">
						<span class="icon">+</span>add media
					</div>
					
					<p><br><br></p>

					<div class="gallery" data-id="<?=$gallery->id?>">
						<?php
						foreach($gallery->media as $item):
						new \Views\GalleryItem($item);
						endforeach;
						?>	
					</div>

				</div>
				<!-- end gallery tab -->
				
				<?php
				endforeach;
				endif;
				?>
				<!-- galleries -->

			</div>
			<!-- end tabs -->
			
		</div>
		<!-- end container -->
		
	<?php
	}
}
?>