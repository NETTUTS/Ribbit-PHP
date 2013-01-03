<div id="createRibbit" class="panel right">
    <h1>Create a Ribbit</h1>
    <p>
        <form action="/ribbit" method="post">
            <textarea name="text" class="ribbitText"></textarea>
            <input type="submit" value="Ribbit!">
        </form>
    </p>
</div>
        <div id="ribbits" class="panel left">
				<h1>Your Ribbit Profile</h1>
				<div class="ribbitWrapper">
					<img class="avatar" src="http://www.gravatar.com/avatar/<?php echo $User->gravatar_hash; ?>">
					<span class="name"><?php echo $User->name; ?></span> @<?php echo $User->username; ?>
					<p>
						<?php echo $userData->ribbit_count . " "; echo ($userData->ribbit_count != 1) ? "Ribbits" : "Ribbit"; ?>
                        <span class="spacing"><?php echo $userData->followers . " "; echo ($userData->followers != 1) ? "Followers" : "Follower"; ?></span>
                        <span class="spacing"><?php echo $userData->following . " Following"; ?></span><br>
						<?php echo $userData->ribbit; ?>
					</p>
				</div>
			</div>
			<div class="panel left">
				<h1>Your Ribbit Buddies</h1>
                <?php foreach($fribbits as $ribbit){ ?>
		                <div class="ribbitWrapper">
		                    <img class="avatar" src="http://www.gravatar.com/avatar/<?php echo $ribbit->gravatar_hash; ?>">
		                    <span class="name"><?php echo $ribbit->name; ?></span> @<?php echo $ribbit->username; ?> 
	                        <span class="time">
	                        <?php 
                                $timeSince = time() - strtotime($ribbit->created_at); 
                                if($timeSince < 60)
                                {
                                    echo $timeSince . "s";
                                }
                                else if($timeSince < 3600)
                                {
                                    echo floor($timeSince / 60) . "m";
                                }
                                else if($timeSince < 86400)
                                {
                                    echo floor($timeSince / 3600) . "h";
                                }
                                else{
                                    echo floor($timeSince / 86400) . "d";
                                }
                            ?>
	                        </span>
	                        <p><?php echo $ribbit->ribbit; ?></p>
		                </div>
		      <?php } ?>			
			</div>