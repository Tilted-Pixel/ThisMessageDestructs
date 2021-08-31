<section class="container">
	<article style="text-align:center">			

				
			<?php
				if( !empty($TemplateVars['linkCreated']) )
				{
					echo '<h1 style="font-weight:bold">Congratulations, your self-destructing message is ready!</h1>';
					echo '<p>Your link is ready in the box below. Simply copy-paste it into e-mail, instant messenger, or even strap it to a carrier pigeon!</p>';
					echo '<textarea style="width:400px" onclick="this.select()">' . htmlspecialchars($TemplateVars['link']) . '</textarea>';
					echo '<p>Remember, the message will destruct once opened!</p>';
				}
				else
				{
					 echo '<h1 style="font-weight:bold">Create Messages that Destruct Upon Reading</h1>';
				}
			?>
			
			<form name="input" action="/?make=1" method="post" autocomplete="off" style="text-align:center">
				
				<?php if( $TemplateVars['linkCreated'] ): ?>
				<h2 style="font-weight:bold">Create another link...</h2>				
				<?php else: ?>
				<h2 style="font-weight:bold">Enter the self-destructing text you wish to share...</h2>
				<?php endif; ?>

				<label for="msg" style="font-weight:bold">Your Message</label><br />
				<textarea name="msg" id="msg" maxlength="50000" style="width:500px; height:100px"></textarea>
				<br /><br />
				<input type="submit" value="CREATE MY MESSAGE LINK" />
			</form>		
    </article>
</section>

<section class="content-section">
        <div class="container">
            <article>
            	<h1>How do I create a self-destructing message?</h1>
            	
            	<ol>
            		<li>Enter the text that you wish to send into the form above.</li>
            		<li>A <strong>secret</strong> link will be generated for you.</li>
            		<li>Copy-paste the link and send it to your intended recepient via e-mail, instant messenger, etc.</li>
            		<li>After the person views your message the data is erased and viewing the link again will not work!</li>
            	</ol>                        
            	
                <h1>Why use a self-destructing note?</h1>
              <p>We built this service to make it easy to send information between people via e-mail or instant messenger without having it sitting around forever in someone's mailbox or instant messenger log. </p>
              
            <p>For example if you create an account for someone, that person <strong>should</strong> change their password to something only they know. But many systems don't enforce this. If you send the password via e-mail, that password might be in someone's mailbox, unencrypted, for months or years, and backed up on countless computer systems. A self-destructing note leaves a smaller trail.</p>
              
              <h1>How secure is this service?</h1>              
            
            <p>This service aims to <em>improve</em> upon regular e-mail and instant messenging services by providing a way to send information that won't sit around for ages or get stored by a 3rd party service (like cloud-based e-mail or your instant messenging program). And doing so without requiring special programs or complex instructions!</p>
                        
            <p>However <strong>no system can ever be fully secure and transmitting your data eletronically carries risks</strong>!By using this service you are agreeing to the <a href="/termsofservice.php" target="_blank"><a href="/termsofservice.php" target="_blank">Terms of Service</a>.</a></p>            
            
           	<p><strong>Short version of what happens to your data:</strong></p>
            
            <ul>
            	<li>The messages that you create are encrypted before being stored in the service.</li>
            	<li>The key used to encrypt your message is part of the link that you receive. We do not store a copy of the key and <strong>can't</strong> decrypt your message.</li>
            	<li>This service uses an encrypted <em>https</em> connection (just like ecommerce websites) to transmit your data back and forth.</li>
            </ul>              	              	
              	                          	              	                          	
             <p><strong>Long version of what happens to your data:</strong></p>
              
             <p>Read our <a href="/security.php">security overview</a> for a full discussion on how the service security works and access to the source code. We welcome and encourage feedback. The message storage and destruction system is open source software so you are also encouraged to review the source yourself.</p>
              
            <h1>Does the service keep people from copy-pasting data?</h1>
            
            <p>Absolutely not. Once your recepient has the data, they can use it however they wish. The point of the service is to get the data to the recepient, not to control how it's used by that person.</p>
              
						<h1>What can I send through this service?</h1>
						
						<p>That's up to you! We hope you find it useful and look forward to your feedback. You should
						   read our <a href="/termsofservice.php" target="_blank">Terms of Service</a> though.</p>
                
								
                <div class="clearfix"></div>
            </article>
        </div>
    </section>