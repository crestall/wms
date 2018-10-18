
	<?php if(empty($users)){ ?>
		<tr class='no-data'><td colspan='4' class='text-muted text-center'>There is no users</td></tr>
	
	<?php }else{
			foreach($users as $user){?>
				<tr id="user-<?php echo Encryption::encryptId($user["id"]); ?>">
					<td><span class="text-primary"><?php echo $user["name"]; ?></span></td>
					<td><span class="label label-primary"><?php echo ucfirst($user["role"]); ?></span></td>
					<?php if(empty($user["email"])){ ?>
							<td class='text-danger'>Not Available</td>
					<?php } else{?>
							<td ><em><?php echo $this->encodeHTML($user["email"]); ?></em></td>
					<?php }?>
					<td class="text-center">
						<span class="pull-right btn-group btn-group-xs">
							<a href="<?php echo PUBLIC_ROOT . "Admin/viewUser/". urlencode(Encryption::encryptId($user["id"]));?>"  class="btn btn-default">
								<i class="fa fa-pencil"></i>
							</a>
							
							<?php 
								// current admin can't delete himself!
								if(Session::getUserId() !== $user["id"]){?>
									<a class="btn btn-danger delete"><i class="fa fa-times"></i></a>
							<?php }?>
						</span>
					</td>
				</tr>	
	<?php }
		}?>

		
