<?php
/* Application Content View PHP */

class Views_ApplicationContent {
	public function __construct() {
		?>

		<body>

			<!-- header -->
			<header>

				 <h1>

				 	<div id="floatingBarsG" class="loading-indicator">
						<div class="blockG" id="rotateG_01">
						</div>
						<div class="blockG" id="rotateG_02">
						</div>
						<div class="blockG" id="rotateG_03">
						</div>
						<div class="blockG" id="rotateG_04">
						</div>
						<div class="blockG" id="rotateG_05">
						</div>
						<div class="blockG" id="rotateG_06">
						</div>
						<div class="blockG" id="rotateG_07">
						</div>
						<div class="blockG" id="rotateG_08">
						</div>
					</div>

				 rearend <b>manager </b> 3 | <a class="logout">logout</a></h1>

				<!-- main navigation -->
				<nav>
					<ul>
						<li class="current" data-label="documents">documents</li>
						<li data-label="media">media</li>
<!-- 						<li data-label="media">settings</li> -->
					</ul>
				</nav>
				<!-- end main navigation -->

			</header>
			<!-- end header -->

			<!-- content wrapper -->
			<div id="content-wrapper">
				<div id="content">

					<!-- main 1 -->
					<div id="main-1" class="section list">
						<div class="section-container">
						<!-- hier komt het hoofd menu -->
						</div>
						<div class="footer">

							<div class="menu">
								<div class="button button-delete menu-item-deleteallimages" title="Delete All"></div>
							</div>

						</div>
					</div>
					<!-- end main 1 -->

					<!-- main 2 -->
					<div id="main-2" class="section list">
						<div class="section-container">
						<!-- hier komt de document / media lijst -->
						</div>
						<div class="footer">

							<div class="menu">
								<div class="button button-create" title="Add Item(s)">
								</div>
							</div>
						</div>
					</div>
					<!-- end main 2 -->

					<!-- main 3 -->
					<div id="main-3" class="section view">
						<div class="section-container">
						<!-- hier komt het document / media item -->
						</div>

						<div class="footer">
							<input id="addImages" type="file" accept="image/*" multiple/>
							<!-- footer element -->
							<div class="menu">
								<div class="button button-save button-saveitem" title="Save">
								</div>
							</div>

							<div class="menu">
								<div class="button button-settings button-docsettings" title="Document actions">

									<div class="dropdown">
										<ul>
											<li class="menu-item-removemedia">Remove Selected Images</li>
										</ul>
									</div>

								</div>

								<div class="button button-delete menu-item-deletedoc" title="Delete"></div>
							</div>

						</div>

					</div>
					<!-- end main 3 -->

				</div>
			</div>
			<!-- end content wrapper -->

			<!-- ajax loader -->
			<div id="ajaxLoader"></div>
			<!-- end ajax loader -->

			<!-- uploads-progress -->
			<div id="uploads-progress">
				<h2>upload media</h2>
				<h3>reading files</h3>
			</div>
			<!-- uploads-progress -->

		</body>
		</html>

		<?php
	}
}
?>