<section class="container">
	<article style="text-align:center">			
    	
<?php

    if( $TemplateVars['retrieved'] == false ) {

        echo <<<'FORM'
        <h1 style="font-weight:bold">Retrieve Your Self-Destructing Message</h1>
        <p><strong>Click the button below to retrieve your secret message. Copy it somewhere safe because it will only work once!</strong></p>
        
        <form name="input" method="post" autocomplete="off" style="text-align:center">
        <br />
        <input type="submit" value="RETRIEVE MY MESSAGE" name="read" />
        </form>
        FORM;
    }
    else {
        if ($TemplateVars['foundMsg'] == false) {
            echo '<h1 style="font-weight:bold">Sorry Your Message Was Not Found :(</h1>';
            echo '<p><strong>Perhaps you have already viewed this message?</strong></p>';
        } else {
            echo '<h1 style="font-weight:bold">This Message Will Self Destruct!</h1>';
            echo '<p>Your message has been retrieved and can be viewed in the box below. If you need it, make sure you copy-paste it somewhere safe! For your protection the message has been erased from our servers and this link will not work again.</p>';
            echo '<textarea style="width:50%;min-height:200px">' . htmlspecialchars(
                    trim($TemplateVars['msgText'])
                ) . '</textarea>';
            echo '<h3 style="font-weight:bold;">IMPORTANT: Save the data above! Opening this link again will not work.</h3>';

            echo '<p><a href="/" class="bigger-button">CREATE YOUR OWN SELF-DESTRUCTING MESSAGE</a></p>';
        }
    }

?>

    </article>
</section>

<section class="content-section">
        <div class="container">
            <article>
            	<h1>What is this website?</h1>
            	<p>ThisMessageDestructs.com allows you to create your own self-destructing message that gets destroyed the first time it is read.<br /><a href="/" style="font-weight:bold">Learn more.</a></p>
            </article>
        </div>
    </section>