<?php
						$script_file	= dirname($_SERVER["SCRIPT_FILENAME"]);
						$site_root		= "/home/libertas/public_html";
						$script		= "index.php";
						$mode		 = "EXECUTE";
						$extra		 = Array("information_list"=>"6");
						$command	 = "INFORMATION_SHOW_IT";
						$fake_title = "Featured Company";
						require_once "/home/libertas/public_html/admin/include.php";
						require_once "$module_directory/included_page.php";
					?>