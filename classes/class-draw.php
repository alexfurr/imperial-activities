<?php
class imperialChatbotDraw
{

	public  static function drawChatbot()
	{
		$str = '
		
		
		
		<div class="grid">
	<div class="bot">
	<div class="title">
		<h2>Imperial Welfare Bot</h2>
	</div>
  		<div id="response-container">
  			<div id="response"></div>
		</div>
 		<br />
    	<input id="botInput" type="text" placeholder="Hi, how can I help?" autocomplete="off" />
	</div>

</div>';
		
		
		return $str;
	}

	
}

?>