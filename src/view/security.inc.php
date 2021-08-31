<section class="container">
	<article style="text-align:center">			
    <h1 style="font-weight:bold">How The Service Works: Security &amp; Source Code</h1>
    <p>The information contained here is highly technical. For general information about the 
            		service <a href="/">go here</a>.</p>
    </article>
</section>

<section class="content-section">
        <div class="container">
            <article>            	            	            	
            	<h1>Downloading the Source Code</h1>
            	<p>The source code for the website (minus our own specific HTML theme) is open sourced under the GPLv3 license. Grab it from our GitHub repository:</p>
            	
                <p><a href="https://github.com/Tilted-Pixel/ThisMessageDestructs">https://github.com/Tilted-Pixel/ThisMessageDestructs</a></p>
            	
            	<h1>Why this Service Exists</h1>
            	
            	<h2>First a little note on the goals of the project.</h2>
            	
            <p>We built this website to solve a problem that we kept encountering at <a href="http://www.tiltedpixel.com" target="_blank" title="website design">Tilted Pixel</a>: how do we share somewhat sensitive data with our clients without making them jump through hoops to send or retrieve it?</p> 
            
            <p>Even though many people do it, using e-mail to send confidential data isn't a hot idea because e-mail itself is shockingly insecure. Many people's inboxes are stored unencrypted on their computer, and even if your mailbox is properly protected, your mail server, or the mail servers the e-mail went through to get to yours, might not be. But, everyone uses e-mail, and security is often trumped by ease-of-use.</p>
            
            <p><strong>But security IS important, and it CAN affect you. We wanted to build something that:</strong></p>
            
            <ul>
            	<li>Doesn't leave sensitive information permanently stored on systems with questionable security (such as people's computers, smartphones, etc).</li>
            	<li>Can be understood and used easily by almost anyone.</li>
            	<li>Allows clients to send data back to us.</li>
            	<li>Can be used on all the major devices and operating systems without prior setup.</li>
            	<li>Looks professional and inspires confidence in the recepient (our intended use case involves our clients for example).</li>
            </ul>            
            
          <p>Some quick online searching found a few alternatives, but in the end we wrote our own because this is actually a pretty interesting problem. Plus we're picky about how things are done and many of the services also didn't release source code, which made us uncomfortable.</p>
            	
            	<h1>Under the Hood: Step-by-Step of What Happens to Your Message</h1>
            	
            	<h2>1. The user's secret message (M0) is submitted to our server over encrypted connection.</h2>
            	
            	<p>The system enforces https:// connection to ensure that your message is only sent encrypted.</p>
            	
            	<h2>2. Message is encrypted and stored.</h2>
            	
            	<p>The server generates a random 128 bit AES key (K0). Random generation is done via openssl_random_pseudo_bytes() as recommended by the php.net site. </p>
            		
            	<p>This key is used to encrypt M0 with 128 bit AES in CBC mode (giving us M1). M1 is stored in the database and identified by identifier ID0 (currently just a mySQL auto increment column but it could be anything).</p>
            	
            	<p>The server concatenates together the API version used to generate this link, the encryption key K0, and the encrypted message M1. This new string is then encrypted with AES-CBC again, this time using a server-wide 128 bit AES key (K1) and iv, giving us M2. </p>
            	
            	<p>The server also creates an HMAC (H0) out of M2 and stores that in the database alongside M1. This is so that we can verify on retrieval that a link has not
            	been tampered with.</p>
            
            	<p><strong>Note that M0 is never stored permanently.</strong></p>
            	
            	<h2>3. Link is Generated and Displayed</h2>
            	<p>M2 is used directly in the link, creating a link like:</p>
            	
            	<p>https://thismessagedestructs.com/m/d8bb873de7d020ac4ad67e8f57cb16ad8c198d91cb3df1488dc33ee9f2087747</p>
            	
            	<p>Everything after /m/ is simply M2.</p>
            	
            	<p>Sender of message can now send this link to the recipient over any communication channel that is unlikely to be actively compromised (such as email or IM).</p>
            	
            	<h2>4. Link is Opened by Recipient</h2>
            	
            	<p>As described above the link contains M2 directly so the server is able to read it and decrypt it using the server wide K1. This gives us the version number, K0 (the key to decrypting M1), and the message identifier ID0.</p>
            	
            	<p>Using ID0 the server is able to retrieve M1 and H0, representin the encrypted message and the HMAC of M2 respectively. The server compares H0 against HMAC of M2 to make sure they match. If they don't match we don't proceed any further. Most likely someone is trying to generate links that happen to match up with valid message ids. This wouldn't allow them to read the message, but it would cause the system to delete it.</p>
            	
            	<p>M1 is now decrypted with K0 and the result is sent over HTTPS to the client. The entry is also deleted from the database (your message has now destructed).</p>
            	
            	
            	<h1>Security FAQ</h1>
            	
            <h2>Why bother creating M2 instead of sending back M1 along with plaintext version and message identifier?</h2>
            
            <p>Since we don't know what the secret message is, we don't actually know if we have decrypted it. We have to assume that if someone sends us a link requesting a valid message id, that the encryption key worked and that we should delete the message.</p>
            
          <p>If the message identifier wasn't encrypted, it would be trivial to troll the website by systematically accessing links with different message identifiers and random (bad) encryption keys. We then back this up with the HMAC as described above to ensure that the encrypted string hasn't been tampered with either.</p>
          
          <p>It's possible we don't need the extra encrypt and that the HMAC would suffice, but exposing the version number and message id could still give clues about an old link, such as when it was generated, or have consequences we haven't predicted, so the encryption feels right.</p>
            	
            	<h2>Why not use 256 bit AES instead of 128 bit?</h2>
            	<p>It comes down to trying to keep the link size small. 128 bit AES is not necessarily inferior: see discussion here.</p>
            	
            	<h2>What about client-side encryption so that your server doesn't have to touch unencrypted data at all?</h2>
            	
            	<p>It's something we are investigating and will be experimenting with, and we will implement it unless we find a reason not to. Keep in mind the service is currently in beta, so things like this will change as it evolves!</p>
            	
            	<p>The process certainly becomes more complex: we need to generate the link such that the message identifier is secret (a troll can't simply iterate through a predictable list of links and nuke everyone's messages despite not having encryption key), the resulting link is not too long, and the final link is generated by the client (so that the decryption key is never sent to the server).</p>
            	
            	<p>The obvious flow would be something like this (leaving out some details like making sure to deal with API versions somehow):</p>
            
            	<ul>
            		<li>User enters secret message (M0) into website form.</li>
	            	<li>Client generates a random 128 bit AES key (K0) and iv (IV0) and uses it to encrypt the secret message (M1).</li>     	            
	            	<li>Client sends M1 and IV0 to the server asking it to be stored.</li>
	            <li>Server stores M1 and generates a message identifer (ID0). ID0 is encrypted using a server-wide AES key (ID1).</li>
	            	<li>Server sends ID1 back to the client.</li>
	            	<li>Client generates a url that includes K0 and ID1 and displays it to the user. </li>	          
            	</ul>
            	
            <p><strong>Retrieving Message</strong></p>
            	
            <ul>
            	<li>Client sends ID1 to the server.</li>
            <li>Server decodes ID1 into ID0 and uses it to retrieve M1 and the IV0.</li>
            <li>M1 and IV0 are sent back to the client.</li>
            <li>Client uses K0 and IV0 to decrypt M1.</li>
            </ul>
            	
            <p>The server never touches the unencrypted text in the above example. This is good if you don't trust the server. Yet the server still plays important role of storing the encrypted message and the iv.</p>
            
            <p>On the other hand there is now a greater responsibility placed on the client (such as ensuring that randomly generated key is in fact random). Could probably be further improved by negotiating an iv.</p>
            	
            	<p><strong>Potential Pitfalls</strong></p>
            	
            	<ul>
            		<li>Assuming this is a website, client-side imposes Javascript requirement. Need to investigate whether mobile devices pose any issues here.</li>
            		<li>Have to put extra care into making sure message identifier is long enough that chances of guessing an encrypted identifier are low.</li>   	
            	</ul>
            	
            <p>Again, we welcome feedback on the above and are open to implementing a client-side version.</p>
            	
            	<h2>What about securily transmitting the link itself?</h2>
            	
            <p>Chances are that you have a reasonably secure way to transmit the link, since even e-mail is unlikely to be compromised at the same time as you are sending along the link (we're way more concerned about what happens months or years later). It's also not possible to read the message without destroying it, thus giving that there is a potential eavesdropper.</p>
              
              <p>If you are concerned about sending the link via e-mail, you do have some options:</p>
              
              <ol>
	              <li>Don't send the full text over the service. For example if sending a password, don't send the username or website that it's for.</li>
              	<li>Use a corporate intranet, project management system, or other service that you and your recepient both have access to, and which is likely to be secure.</li>
              	<li>Encrypt your e-mails. </li>
              <li>A future version of the service will have an (optional) passphrase that you can specify must be entered by the recepient (and which you DON'T send over e-mail), thus creating 2 factor authentication.</li>
              </ol>
            	
            	<h2>What if I just don't trust you?</h2>
            	
            	<p>Well you didn't have to be so blunt about it, but it's true that security shouldn't require blind trust! This software is open source: you can download the <a href="/security.php">source code</a>, verify how it works, and setup your own instance of the service.</p>
            	
            	<p>We are also working on a client-side version of the scheme that allows the message to be encrypted prior to being sent to the server. This would allow you to verify the Javascript code does what it claims to prior to using the service. Hardcore.</p>
            	
            	<h1>Feedback is Welcome!</h1>
            	
            	<p>Building version 1 was a fun exercise and we're already putting this website to good use in our own work. We very much welcome feedback, both on the user-facing side, and on the technical side. E-mail <a href="mailto:tmd@tiltedpixel.com">tmd@tiltedpixel.com</a> with your thoughts!</p>
            	
            </article>
        </div>
    </section>