	<div class="footer">
		<div class="copyright">© 2012 Компания «Art Decor»<br/>+7(925) 205-75-66</div>
		<div class="footermenu">
			<ul>
				<? 
					$top_menu_array	= explode("</li>", $menu);
					$top_menu_left	= array();
					$top_menu_right	= array();
					
					for($i=0; $i<3; $i++) {
						$top_menu_left[] = $top_menu_array[$i].'</li>';
					}
					for($i=3; $i<6; $i++) {
						$top_menu_right[] = $top_menu_array[$i].'</li>';
					}
					$top_menu_left	= join('', $top_menu_left);
					$top_menu_right	= join('', $top_menu_right);
				?>
				<?=$top_menu_left;?>
				<?=$top_menu_right;?>
			</ul>
		</div>
	</div>
</div>
</body>
</html>