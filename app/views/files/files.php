
	<?php if(empty($files)){ ?>
		<tr class='no-data'><td colspan='4' class='text-muted text-center'>There is no files!</td></tr>
	
	<?php } else{
			foreach($files as $file){?>
			
				<tr id="<?php echo "file-" . Encryption::encryptId($file["id"]); ?>">
					<td style="width: 20%;"><strong><?php echo $file["user_name"];?></strong><br>
						<em><?php echo $this->timestamp($file["date"]);?></em><br>
					</td>
					
					<td><a href="<?php echo PUBLIC_ROOT . "downloads/download/" . urlencode($file["hashed_filename"]); ?>">
						<strong><?php echo  $this->truncate($this->encodeHTML($file["filename"]),20); ?></strong></a>
					</td>
					
					<td class="text-muted"><?php echo strtoupper($file["format"]); ?></td>
					
					<?php if(Session::getUserId() === (int) $file["user_id"] || Session::getUserRole() === "admin"){ ?>
							<td class="text-center"><a class="btn btn-danger btn-xs delete"><i class="fa fa-times"></i></a></td>
					<?php }?>
				</tr>	
	<?php }
		}?>

